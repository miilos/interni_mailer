<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;

class EmailBuilderService
{
    private Email $email;

    public function createEmail(bool $usesTemplate): Email|TemplatedEmail
    {
        if ($usesTemplate) {
            $this->email = new TemplatedEmail();
        }
        else {
            $this->email = new Email();
        }

        return  $this->email;
    }

}
