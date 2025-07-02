<?php

namespace App\Message;

use App\Dto\EmailDto;

class SendEmail
{
    public function __construct(private EmailDto $email) {}

    public function getEmail(): EmailDto
    {
        return $this->email;
    }
}
