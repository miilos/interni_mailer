<?php

namespace App\Command;

use App\Dto\EmailDto;
use App\Service\MailerService;

class SendEmailCommand implements CommandInterface
{
    private $emailDto;

    public function __construct(
        private MailerService $mailerService
    ) {}

    public function execute(): void
    {
        $this->mailerService->send($this->emailDto);
    }

    public function setEmail(EmailDto $dto): void
    {
        $this->emailDto = $dto;
    }
}
