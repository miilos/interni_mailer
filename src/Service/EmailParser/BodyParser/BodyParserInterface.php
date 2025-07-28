<?php

namespace App\Service\EmailParser\BodyParser;

interface BodyParserInterface
{
    public function parseTemplate(string $templateContent): string;
    public function supports(string $extension): bool;
}
