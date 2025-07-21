<?php

namespace App\Service\EmailParser;

interface BodyParserInterface
{
    public function parseTemplate(string $templateContent): string;
}
