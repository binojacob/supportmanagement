<?php

namespace App\Controller;

use App\Entity\SupportCase;
use App\Repository\CommentsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SupportCaseRepository;
class CommentsController extends AbstractController
{
    #[Route('/list_comments/{case}', name: 'app_comments')]
    public function index(CommentsRepository $commentRepo,SupportCase $case,SupportCaseRepository $support): Response
    {
        $case_id = $case->getId();
        $cases = $support->findBy(['id'=>$case_id ]);
       
        $commentList = $commentRepo->findBy(['supportcase'=>$case_id ]);
        return $this->render('comments/index.html.twig', [
            'comments' => $commentList,
            'supportcases' => $cases
        ]);
    }
}
