<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $data = new ContactDTO();
        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //management of send message to mailpit
            try {
                $mail = (new TemplatedEmail())
                    ->to($data->service)
                    ->from($data->email)
                    ->subject('Demand of contact')
                    ->htmlTemplate('contact/email.html.twig')
                    ->context(['data' => $data]);
                $mailer->send($mail);
                $this->addFlash('success', 'Message sent successfully');
                return $this->redirectToRoute('contact');
            } catch (Exception|TransportExceptionInterface $e) {
                $this->addFlash('danger', 'Message not sent'. $e);
            }
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
