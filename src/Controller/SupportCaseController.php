<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\SupportCase;
use App\Entity\User;
use App\Form\CommentsFormType;
use App\Form\SupportCaseFormType;
use App\Repository\CommentsRepository;
use App\Repository\SupportCaseRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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

        // if(!$this->isGranted('ROLE_SPECIALIST') and !$this->isGranted('ROLE_ADMIN')){
        //     throw $this->createAccessDeniedException();
        // }
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
            return $this->redirectToRoute("app_support_case");
        }

        return $this->render('support_case/index.html.twig', [
            'form'=>$form,
            'controller_name' => 'SupportCaseController',
            'supportcases' => $cases
        ]);
    }


    #[Route('/listcase', name: 'app_list_case')]
    public function listCases( SupportCaseRepository $support,Request $request) {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
                ->add('status', ChoiceType::class, [
                    'choices'  => [
                        'Select Status' => '',
                        'Open' => 'open',
                        'InProgress' => "inprogress",
                        'Cancelled' => "cancel",
                    ],
                ])
                ->add('Submit', SubmitType::class)
                ->getForm();

        $form->handleRequest($request);

        $data['status'] = "";
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
        }

        if($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_SPECIALIST')){
            $statusArr = [];
            if($data['status'] !== ""){
                $statusArr = ['status'=>$data['status']];
            }

            $casedata = $support->findBy($statusArr,['id'=>'DESC']);
        }
        else {
            $casedata = $support->findBy(['status'=>'open'],['id'=>'DESC']);
        }

        $user = $this->getUser();
        $userId = $user->getId();

        return $this->render('support_case/listcase.html.twig', [
            'supportcases' => $casedata,
            'userId'=> $userId,
            'form'=>$form
        ]);
    }

    #[Route('/delcase/{case}', name: 'app_del_case')]
    public function deleteCases(SupportCase $case,EntityManagerInterface $em) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $em->remove($case);
        $em->flush();
        return $this->redirectToRoute("app_list_case");
    }

    #[Route('/editcase/{case}', name: 'app_edit_case')]
    public function editCases(SupportCase $case,
    Request $request,EntityManagerInterface $em
    ) {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $user = $case->getUser();

        $form = $this->createFormBuilder($case)
        ->add('description')
        ->add('summary')
        ->add('status', ChoiceType::class, [
            'choices'  => [
                'open' => 'Open',
                'InProgress' => "inprogress",
                'Cancelled' => "cancel",
            ],
        ])
        ->add('Submit',SubmitType::class)
        ->getForm();
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $em->persist($data);
            $em->flush();
            return $this->redirectToRoute("app_list_case");
        }
        return $this->render('support_case/editcase.html.twig', [
            'form' => $form,
            'username'=> $user->getFullName()
        ]);
    }

    #[Route('/addcomments/{case}', name: 'app_add_comments')]
    public function addComments(
        SupportCase $case,SupportCaseRepository $support,
        Request $request,EntityManagerInterface $em) {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

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
            return $this->redirectToRoute("app_list_case");
        }
        
        return $this->render('support_case/addcomments.html.twig', [
            'supportcases' => $cases,
            'form'=> $form
        ]);
    }
    


}
