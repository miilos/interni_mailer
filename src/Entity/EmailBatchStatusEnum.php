<?php

namespace App\Entity;

enum EmailBatchStatusEnum: string
{
    case QUEUED = 'queued';
    case SENT = 'sent';
    case FAILED = 'failed';
}
