<?php

namespace App\Service\EmailParser\BodyParser;

interface BodyParserInterface
{
    public function parseTemplate(string $templateContent, array $variables = []): string;
    public function supports(string $extension): bool;
}
