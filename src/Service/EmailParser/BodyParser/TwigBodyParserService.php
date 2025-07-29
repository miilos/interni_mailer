<?php

namespace App\Service\EmailParser\BodyParser;

use App\Service\EmailParser\BodyParser\BodyParserInterface;
use App\Service\EmailParser\BodyParser\MjmlBodyParserService;
use App\Service\EmailParser\TwigContextBuilderService;
use Twig\Environment;
use Twig\Error\SyntaxError;

class TwigBodyParserService implements BodyParserInterface
{
    public function __construct(
        private Environment $twig,
        private TwigContextBuilderService $twigContextBuilderService,
        private MjmlBodyParserService $mjmlBodyParserService,
    ) {}

    public function supports(string $extension): bool
    {
        return str_contains($extension, 'twig');
    }

    public function parseTemplate(string $templateContent, array $variables = []): string
    {
        try {
            $template = $this->twig->createTemplate($templateContent);
            $context = $this->twigContextBuilderService->getContext($variables);

            $content = $template->render($context);
            if (preg_match('/\b(mjml|mj-[\w-]+)\b/', $content)) {
                $content = $this->mjmlBodyParserService->parseTemplate($content);
            }

            return $content;
        }
        catch (SyntaxError $e) {
            throw new ParserException($e->getMessage(), 400);
        }
    }
}
