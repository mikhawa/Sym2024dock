<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Post;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(EntityManagerInterface $em): Response
    {
        $posts = $em->getRepository(Post::class)->findAll();
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'post' => $posts
        ]);
    }
}
