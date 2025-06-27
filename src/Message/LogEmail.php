<?php

namespace App\Message;

use App\Dto\EmailDto;
use App\Dto\EmailLogDto;

class LogEmail
{
    private EmailLogDto $emailLogDto;

    public function __construct(
        EmailDto $emailDto,
        string $status
    ) {
        $this->emailLogDto = new EmailLogDto($emailDto, $status);
    }

    public function getEmailLogDto(): EmailLogDto
    {
        return $this->emailLogDto;
    }
}
