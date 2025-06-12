<?php

namespace App\Event;

use App\Dto\EmailDto;
use Symfony\Contracts\EventDispatcher\Event;

final class SendEmailEvent extends Event
{
    public function __construct(private EmailDto $email) {}

    public function getEmail(): EmailDto
    {
        return $this->email;
    }
}
