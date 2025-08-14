<?php

namespace App\Message;

use App\Dto\EmailDto;
use App\Dto\EmailLogDto;

class LogEmail
{
    private EmailLogDto $emailLogDto;

    public function __construct(
        EmailDto $emailDto,
        string $to,
        string $status,
        ?string $error = null,
    ) {
        $this->emailLogDto = new EmailLogDto($emailDto, $to, $status, $error);
    }

    public function getEmailLogDto(): EmailLogDto
    {
        return $this->emailLogDto;
    }
}
