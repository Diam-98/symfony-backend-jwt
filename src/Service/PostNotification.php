<?php

namespace App\Service;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class PostNotification
{
    private MailerInterface $mailer;

    /**
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendNotification(string $email): void
    {
        $email = (new Email())
            ->from('diamil.dev@email.com')
            ->to($email)
            ->subject('Nouveau Post créé')
            ->text('Un nouveau post a été créé avec succes');

        $this->mailer->send($email);
    }

}