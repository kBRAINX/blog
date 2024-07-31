<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PostController extends AbstractController
{
    #[Route('/posts', name: 'post.index')]
    public function index(PostRepository $repository, Request $request, Security $security): Response
    {
        $user = $security->getUser();
        $posts = $repository->paginateAll($request);
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'user' => $user
        ]);
    }

    #[Route('/admin/posts', name: 'admin.post.index')]
    #[IsGranted('ROLE_ADMIN')]
    public function home(PostRepository $repository, Request $request, Security $security): Response
    {
        $user = $security->getUser();
        $posts = $repository->paginateAll($request);
        return $this->render('admin/post/index.html.twig', [
            'posts' => $posts,
            'user' => $user
        ]);
    }

    #[Route('/services', name: 'service')]
    public function service(PostRepository $repository, Request $request, Security $security, EntityManagerInterface $em): Response
    {
        $serviceId = $request->query->get('service', 'all');
        $user = $security->getUser();
        $posts = $repository->paginateAll($request);
        $services = $em->getRepository(Category::class)->findAll();

        return $this->render('post/service.html.twig', [
            'posts' => $posts,
            'services' => $services,
            'user' => $user,
            'currentService' => $serviceId
        ]);
    }

    #[Route('/admin/services', name: 'admin.service')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminService(PostRepository $repository, Request $request, Security $security, EntityManagerInterface $em): Response
    {
        $serviceId = $request->query->get('service', 'all');
        $user = $security->getUser();
        $posts = $repository->paginateAll($request);
        $services = $em->getRepository(Category::class)->findAll();

        return $this->render('admin/post/service.html.twig', [
            'posts' => $posts,
            'services' => $services,
            'user' => $user,
            'currentService' => $serviceId
        ]);
    }

    #[Route('/services/filter', name: 'post.filter')]
    public function filterByService(Request $request, PostRepository $repository, Security $security): Response
    {
        $user = $security->getUser();
        $serviceId = $request->query->get('service', 'all');
        $posts = $repository->findByService($serviceId);

        return $this->render('post/post_list.html.twig', [
            'posts' => $posts,
            'user' => $user
        ]);
    }

    #[Route('/admin/services/filter', name: 'admin.post.filter')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminFilterByService(Request $request, PostRepository $repository, Security $security): Response
    {
        $user = $security->getUser();
        $serviceId = $request->query->get('service', 'all');
        $posts = $repository->findByService($serviceId);

        return $this->render('admin/post/post_list.html.twig', [
            'posts' => $posts,
            'user' => $user
        ]);
    }


    #[Route('/posts/{id}', name: 'post.show', requirements: ['id' => Requirement::DIGITS])]
    public function show(Post $post, EntityManagerInterface $em, Request $request, Security $security): Response
    {
        $page = $request->query->getInt('page', 1);
        $comments = $em->getRepository(Comment::class)->findCommentsByPostWithPagination($post, $page);
        $totalComments = $em->getRepository(Comment::class)->countCommentsByPost($post);
        $totalPages = ceil($totalComments / 10);

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setPost($post);
            $comment->setUser($this->getUser());
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('post.show', ['id' => $post->getId()]);
        }

        $user = $security->getUser();
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'comments' => $comments,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'commentForm' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('/admin/posts/{id}', name: 'admin.post.show', requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminShow(Post $post, EntityManagerInterface $em, Request $request, Security $security): Response
    {
        $page = $request->query->getInt('page', 1);
        $comments = $em->getRepository(Comment::class)->findCommentsByPostWithPagination($post, $page);
        $totalComments = $em->getRepository(Comment::class)->countCommentsByPost($post);
        $totalPages = ceil($totalComments / 10);

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setPost($post);
            $comment->setUser($this->getUser());
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('admin.post.show', ['id' => $post->getId()]);
        }

        $user = $security->getUser();
        return $this->render('admin/post/show.html.twig', [
            'post' => $post,
            'comments' => $comments,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'commentForm' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('/posts/create', name: 'post.create')]
    public function new(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $post = new Post();
        $user = $security->getUser();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUser($user);
            $em->persist($post);
            $em->flush();
            $this->addFlash('success', 'Post created successfully.');
            return $this->redirectToRoute('post.index');
        }
        return $this->render('post/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/posts/create', name: 'admin.post.create')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminNew(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $post = new Post();
        $user = $security->getUser();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUser($user);
            $em->persist($post);
            $em->flush();
            $this->addFlash('success', 'Post created successfully.');
            return $this->redirectToRoute('admin.post.index');
        }
        return $this->render('admin/post/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/posts/{id}/edit', name: 'post.edit', requirements: ['id' => Requirement::DIGITS], methods: ['GET', 'POST'])]
    public function edit(Post $post, Request $request, EntityManagerInterface $em, Security $security): Response
    {
        if ($post->getUser() !== $security->getUser()) {
            $this->addFlash('danger', 'You are not allowed to edit this post.');
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Post updated successfully.');
            return $this->redirectToRoute('post.show', ['id' => $post->getId()]);
        }
        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/posts/{id}/edit', name: 'admin.post.edit', requirements: ['id' => Requirement::DIGITS], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminEdit(Post $post, Request $request, EntityManagerInterface $em, Security $security): Response
    {
        if ($post->getUser() !== $security->getUser()) {
            $this->addFlash('danger', 'You are not allowed to edit this post.');
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Post updated successfully.');
            return $this->redirectToRoute('admin.post.show', ['id' => $post->getId()]);
        }
        return $this->render('admin/post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/posts/{id}/delete', name: 'post.delete', requirements: ['id' => Requirement::DIGITS])]
    public function delete(Post $post, EntityManagerInterface $em): Response
    {
        $em->remove($post);
        $em->flush();
        $this->addFlash('success', 'Post deleted successfully.');
        return $this->redirectToRoute('admin.post.index');
    }
}
