<?php

namespace App\Service\EmailParser\BodyParser;

use App\Service\CssSanitizerService;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

class BodyParserService
{
    private iterable $parsers;
    private HtmlSanitizerInterface $sanitizer;
    private CssSanitizerService $cssSanitizer;

    public function __construct(iterable $parsers, HtmlSanitizerInterface $sanitizer, CssSanitizerService $cssSanitizer)
    {
        $this->parsers = $parsers;
        $this->sanitizer = $sanitizer;
        $this->cssSanitizer = $cssSanitizer;
    }

    private function sanitizeTemplate(string $template): string
    {
        return $this->sanitizer->sanitize($template);
    }

    public function parse(string $templateContent, string $extension, array $variables = []): string
    {
        foreach ($this->parsers as $parser) {
            if ($parser->supports($extension)) {
                // parse the template
                $parsedTemplate = $parser->parseTemplate($templateContent, $variables);

                // extract the <head> if it exists because the Symfony Sanitizer discards it
                preg_match('#<head.*?>(.*?)</head>#is', $parsedTemplate, $matches);
                $head = $matches[0] ?? '';

                // sanitize any <style> tags in the <head>
                // this is done without Symfony Sanitizer because it removes <head> and <style> tags
                $sanitizedHead = $this->cssSanitizer->sanitize($head);

                // sanitize the rest of the template
                $sanitizedTemplate = $this->sanitizeTemplate($parsedTemplate);

                // find where the <body> tag starts and insert the head right before it
                $bodyStart = strpos($sanitizedTemplate, '<body');
                $sanitizedTemplate = substr_replace($sanitizedTemplate, $sanitizedHead, $bodyStart, 0);

                return $sanitizedTemplate;
            }
        }

        throw new UnsupportedTemplateFormatException('Only supported template formats are html.twig and mjml.html!', 400);
    }
}
