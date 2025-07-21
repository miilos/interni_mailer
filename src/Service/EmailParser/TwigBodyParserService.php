<?php

namespace App\Service\EmailParser;

use App\Dto\EmailDto;
use Twig\Environment;

class TwigBodyParserService
{
    public function __construct(
        private Environment $twig,
        private TwigContextBuilderService $twigContextBuilderService
    ) {}

    public function renderTemplate(string $templateContent, ?EmailDto $emailDto = null): string
    {
        $context = $this->twigContextBuilderService->getContext($emailDto);
        $template = $this->twig->createTemplate($templateContent);
        return $template->render($context);
    }
}
