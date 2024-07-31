<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $em): Response
    {
        $categories = $em->getRepository(Category::class)->findAll();
        $posts = $em->getRepository(Post::class)->findAll();
        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'posts' => $posts
        ]);
    }

    #[Route('/admin/dashboard', name: 'dashboard')]
    public function dashboard(EntityManagerInterface $em)
    {
        $services = $em->getRepository(Category::class)->findAllAndCountPost();
        $posts = $em->getRepository(Post::class)->findAll();
        $users = $em->getRepository(User::class)->findAll();

        return $this->render('/admin/home/dashboard.html.twig', [
            'services' => $services,
            'posts' => $posts,
            'users' => $users
        ]);

    }
}
