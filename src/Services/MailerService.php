<?php

namespace App\Services;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;    
    }

    public function sendEmail(
        $from = '', 
        $name = '', 
        $template = '', 
        $subject = '', 
        $content = '', 
        $to = 'contact@sos-home-pc.fr'
        ):void {

        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject('Demande de contact de ' . $name)
            ->htmlTemplate($template)
            ->context([
                'nom' => $name,
                'sujet' => $subject,
                'message' => $content
            ]);

        $this->mailer->send($email);

    }

}