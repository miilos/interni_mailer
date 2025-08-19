<?php

namespace App\Service\EmailParser\BodyParser;

use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

class BodyParserService
{
    private iterable $parsers;
    private HtmlSanitizerInterface $sanitizer;

    public function __construct(iterable $parsers, HtmlSanitizerInterface $sanitizer)
    {
        $this->parsers = $parsers;
        $this->sanitizer = $sanitizer;
    }

    private function sanitizeTemplate(string $template): string
    {
        return $this->sanitizer->sanitize($template);
    }

    public function parse(string $templateContent, string $extension, array $variables = []): string
    {
        foreach ($this->parsers as $parser) {
            if ($parser->supports($extension)) {
                $parsedTemplate = $parser->parseTemplate($templateContent, $variables);
                $sanitizedTemplate = $this->sanitizeTemplate($parsedTemplate);
                return $sanitizedTemplate;
            }
        }

        throw new UnsupportedTemplateFormatException('Only supported template formats are html.twig and mjml.html!', 400);
    }
}
