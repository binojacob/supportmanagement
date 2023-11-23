<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\SupportCase;
use App\Form\CommentsFormType;
use App\Form\SupportCaseFormType;
use App\Repository\CommentsRepository;
use App\Repository\SupportCaseRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SupportCaseController extends AbstractController
{
    #[Route('/support/case', name: 'app_support_case')]
    public function index(Request $request,
    SupportCaseRepository $support,
    EntityManagerInterface $em
    ): Response
    {
        if(!$this->isGranted('ROLE_SPECIALIST') and !$this->isGranted('ROLE_ADMIN')){
            print "You not have permission to access this page";exit;
        }
        $user = $this->getUser();
        $userId = $user->getId();
        $cases = $support->findBy(['user'=>$userId ]);


        $support = new SupportCase();
        $form = $this->createForm(SupportCaseFormType::class,$support);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $support->setUser($user);
            $em->persist($data);
            $em->flush();
        }

        return $this->render('support_case/index.html.twig', [
            'form'=>$form,
            'controller_name' => 'SupportCaseController',
            'supportcases' => $cases
        ]);
    }


    #[Route('/listcase', name: 'app_list_case')]
    public function listCases( SupportCaseRepository $support) {
        $casedata = $support->findAll();

        return $this->render('support_case/listcase.html.twig', [
            'supportcases' => $casedata
        ]);
    }

    #[Route('/delcase{case}', name: 'app_del_case')]
    public function deleteCases(SupportCase $case) {
        print "Test";
    }

    #[Route('/editcase/{case}', name: 'app_edit_case')]
    public function editCases(SupportCase $case) {
        dd($case->getId());
    }

    #[Route('/addcomments/{case}', name: 'app_add_comments')]
    public function addComments(
        SupportCase $case,SupportCaseRepository $support,
        CommentsRepository $commentRepo,
        Request $request,EntityManagerInterface $em) {
        $case_id = $case->getId();

        $cases = $support->findBy(['id'=>$case_id ]);
        $comments = new Comments();
        $form = $this->createForm(CommentsFormType::class,$comments);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $comments->setSupportcase($case);
            $em->persist($data);
            $em->flush();
        }

        $commentList = $commentRepo->findBy(['supportcase'=>$case_id ]);
        
        
        return $this->render('support_case/addcomments.html.twig', [
            'supportcases' => $cases,
            'comments' => $commentList,
            'form'=> $form
        ]);
    }
    


}
