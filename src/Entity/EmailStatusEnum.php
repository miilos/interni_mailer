<?php

namespace App\Entity;

enum EmailStatusEnum: string
{
    case SENDING = 'sending';
    case SENT = 'sent';
    case FAILED = 'failed';
}
