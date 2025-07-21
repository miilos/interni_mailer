<?php

namespace App\Service\EmailParser;

use Twig\Environment;

class TwigBodyParserService
{
    private array $context;

    public function __construct(
        private Environment $twig,
        private TwigContextBuilderService $twigContextBuilderService
    ) {
        $this->context = $this->twigContextBuilderService->getContext();
    }

    public function renderTemplate(string $templateContent): string
    {
        $template = $this->twig->createTemplate($templateContent);
        return $template->render($this->context);
    }
}
