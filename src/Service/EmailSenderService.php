<?php

namespace App\Service;

use App\Dto\EmailDto;
use App\Repository\EmailTwigTemplateRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailSenderService
{
    public function __construct(
        private MailerInterface $mailer,
        private EmailTwigTemplateRepository  $emailTwigTemplateRepository,
    ) {}

    public function send(EmailDto $emailDto): void
    {
        $email = null;

        if ($emailDto->getTwigTemplate()) {
            $email = new TemplatedEmail();
            $template = $this->getTwigTemplateFilePath($emailDto->getTwigTemplate())['filePath'];
            $email->htmlTemplate($template);
        }
        else {
            $email = new Email();
            $email->text($emailDto->getBody());
        }

        $email
            ->from($emailDto->getFrom())
            ->subject($emailDto->getSubject());

        if ($emailDto->getCc()) {
            foreach ($emailDto->getCc() as $cc) {
                $email->addCc($cc);
            }
        }

        if ($emailDto->getBcc()) {
            foreach ($emailDto->getBcc() as $bcc) {
                $email->addBcc($bcc);
            }
        }

        foreach ($emailDto->getTo() as $to) {
            $email->to($to);
            $this->mailer->send($email);
        }
    }

    private function getTwigTemplateFilePath(string $templateName): array
    {
        return $this->emailTwigTemplateRepository->getEmailTwigTemplateByName($templateName);
    }
}
