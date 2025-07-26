<?php

namespace App\Service\EmailParser;

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

    public function parseTemplate(string $templateContent): string
    {
        $template = $this->twig->createTemplate($templateContent);
        $content = $template->render($this->context);
        $html = $this->mjmlBodyParserService->parseTemplate($content);
        return $html;
    }
}
