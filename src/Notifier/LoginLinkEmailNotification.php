<?php

namespace App\Notifier;

use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkDetails;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;

class LoginLinkEmailNotification extends LoginLinkNotification
{
    public function __construct(
        private string $loginUrl,
        private LoginLinkDetails $loginLinkDetails,
        string $subject,
        array $channels = [],
    ) {
        parent::__construct($this->loginLinkDetails, $subject, $channels);
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        $emailMessage = parent::asEmailMessage($recipient, $transport);

        $email = $emailMessage->getMessage();
        $email->htmlTemplate('emails/login_link_email.html.twig');
        $email->context([
            'login_link_url' => $this->loginUrl,
        ]);

        return $emailMessage;
    }
}
