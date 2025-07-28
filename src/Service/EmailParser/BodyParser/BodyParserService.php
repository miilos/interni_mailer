<?php

namespace App\Service\EmailParser\BodyParser;

class BodyParserService
{
    private iterable $parsers;

    public function __construct(iterable $parsers)
    {
        $this->parsers = $parsers;
    }

    public function parse(string $templateContent, string $extension): string
    {
        foreach ($this->parsers as $parser) {
            if ($parser->supports($extension)) {
                return $parser->parseTemplate($templateContent);
            }
        }

        throw new UnsupportedTemplateFormatException('Only supported template formats are html.twig and mjml.html!', 400);
    }
}
