<?php

namespace App\Service\WebhookParser;

use App\Dto\WebhookDto;

interface WebhookParserInterface
{
    public function supports(array $webhook): bool;
    public function parse(array $webhook): WebhookDto;
}
