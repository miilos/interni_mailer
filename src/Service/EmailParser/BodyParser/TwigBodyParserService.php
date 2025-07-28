<?php

namespace App\Service\EmailParser\BodyParser;

use App\Service\EmailParser\BodyParser\BodyParserInterface;
use App\Service\EmailParser\BodyParser\MjmlBodyParserService;
use App\Service\EmailParser\TwigContextBuilderService;
use Twig\Environment;

class TwigBodyParserService implements BodyParserInterface
{
    private array $context;

    public function __construct(
        private Environment $twig,
        private TwigContextBuilderService $twigContextBuilderService,
        private MjmlBodyParserService $mjmlBodyParserService,
    ) {
        $this->context = $this->twigContextBuilderService->getContext();
    }

    public function supports(string $extension): bool
    {
        return str_contains($extension, 'twig');
    }

    public function parseTemplate(string $templateContent): string
    {
        $template = $this->twig->createTemplate($templateContent);
        $content = $template->render($this->context);

        if (preg_match('/\b(mjml|mj-[\w-]+)\b/', $content)) {
            $content = $this->mjmlBodyParserService->parseTemplate($content);
        }

        return $content;
    }
}
