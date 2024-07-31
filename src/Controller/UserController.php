<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    #[Route('/users', name: 'admin.user.index')]
    public function index(UserRepository $repository): Response
    {
        $users = $repository->findAll();
        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/users/{id}/delete', name: 'admin.user.delete')]
    public function delete(User $user, EntityManagerInterface $em): RedirectResponse
    {
        $em->remove($user);
        $em->flush();
        $this->addFlash('success', 'User deleted successfully.');
        return $this->redirectToRoute('admin.user.index');
    }
}
