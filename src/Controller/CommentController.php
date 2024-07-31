<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CommentController extends AbstractController
{
    #[Route('/comment/{id}/create', name: 'comment.create')]
    public function create(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $post = $em->getRepository(Post::class)->findOneBy(['id' => $request->get('id')]);
        $comment = new Comment();
        $user = $security->getUser();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $comment->setUser($user);
            $comment->setPost($post);
            $em->persist($comment);
            $em->flush();
            $this->addFlash('success', 'Comment created successfully.');
            return $this->redirectToRoute('post.show', ['id' => $post->getId()]);
        }
        return $this->render('comment/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/comment/{id}/create', name: 'admin.comment.create')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminCreate(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $post = $em->getRepository(Post::class)->findOneBy(['id' => $request->get('id')]);
        $comment = new Comment();
        $user = $security->getUser();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $comment->setUser($user);
            $comment->setPost($post);
            $em->persist($comment);
            $em->flush();
            $this->addFlash('success', 'Comment created successfully.');
            return $this->redirectToRoute('admin.post.show', ['id' => $post->getId()]);
        }
        return $this->render('admin/comment/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/comment/{id}/edit', name: 'comment.edit', requirements: ['id' => Requirement::DIGITS])]
    public function update(Comment $comment, Request $request, EntityManagerInterface $em, Security $security): Response
    {
        if ($comment->getUser() !== $security->getUser()) {
            $this->addFlash('danger', 'You are not allowed to edit this comment.');
        }

        $post = $comment->getPost();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success', 'Comment updated successfully.');
            return $this->redirectToRoute('post.show', ['id' => $post->getId()]);
        }
        return $this->render('comment/edit.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment
        ]);
    }

    #[Route('/admin/comment/{id}/edit', name: 'admin.comment.edit', requirements: ['id' => Requirement::DIGITS])]
    public function adminUpdate(Comment $comment, Request $request, EntityManagerInterface $em, Security $security): Response
    {
        if ($comment->getUser() !== $security->getUser()) {
            $this->addFlash('danger', 'You are not allowed to edit this comment.');
        }

        $post = $comment->getPost();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success', 'Comment updated successfully.');
            return $this->redirectToRoute('admin.post.show', ['id' => $post->getId()]);
        }
        return $this->render('admin/comment/edit.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/comment/{id}/delete', name: 'comment.delete', requirements: ['id' => Requirement::DIGITS])]
    public function delete(Comment $comment, EntityManagerInterface $em, Security $security): Response
    {
        $em->remove($comment);
        $em->flush();
        $this->addFlash('success', 'Comment deleted successfully.');
        return $this->redirectToRoute('post.show', ['id' => $comment->getPost()->getId()]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/comment/{id}/delete', name: 'admin.comment.delete', requirements: ['id' => Requirement::DIGITS])]
    public function adminDelete(Comment $comment, EntityManagerInterface $em, Security $security): Response
    {
        $em->remove($comment);
        $em->flush();
        $this->addFlash('success', 'Comment deleted successfully.');
        return $this->redirectToRoute('admin.post.show', ['id' => $comment->getPost()->getId()]);
    }
}
