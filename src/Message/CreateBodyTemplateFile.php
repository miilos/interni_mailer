<?php

namespace App\Message;

use App\Dto\EmailBodyDto;

class CreateBodyTemplateFile
{
    public function __construct(
        private EmailBodyDto $emailBodyDto,
    ) {}

    public function getEmailBody(): EmailBodyDto
    {
        return $this->emailBodyDto;
    }
}
