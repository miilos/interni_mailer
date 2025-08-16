<?php

namespace App\Service\WebhookParser;

use App\Dto\WebhookDto;
use http\Exception\RuntimeException;

class WebhookParserService
{
    private iterable $parsers;

    public function __construct(iterable $parsers)
    {
        $this->parsers = $parsers;
    }

    public function parse(array $webhook): WebhookDto
    {
        foreach ($this->parsers as $parser) {
            if ($parser->supports($webhook)) {
                return $parser->parse($webhook);
            }
        }

        throw new RuntimeException('Unable to parse webhook!');
    }
}
