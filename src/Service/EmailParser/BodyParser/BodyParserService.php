<?php

namespace App\Service\EmailParser\BodyParser;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class BodyParserService
{
    private iterable $parsers;
    private HtmlSanitizer $sanitizer;

    public function __construct(iterable $parsers)
    {
        $this->parsers = $parsers;
        $this->sanitizer = new HtmlSanitizer(
            (new HtmlSanitizerConfig())->allowSafeElements()
        );
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
                $sanitizedTemplate = $this->sanitizer->sanitize($parsedTemplate);
                return $sanitizedTemplate;
            }
        }

        throw new UnsupportedTemplateFormatException('Only supported template formats are html.twig and mjml.html!', 400);
    }
}
