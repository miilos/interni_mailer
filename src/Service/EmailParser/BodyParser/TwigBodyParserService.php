<?php

namespace App\Service\EmailParser\BodyParser;

use App\Service\EmailParser\BodyParser\BodyParserInterface;
use App\Service\EmailParser\BodyParser\MjmlBodyParserService;
use App\Service\EmailParser\TwigContextBuilderService;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Twig\Environment;
use Twig\Error\SyntaxError;
use Twig\Extension\SandboxExtension;
use Twig\Loader\ArrayLoader;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityPolicy;

class TwigBodyParserService implements BodyParserInterface
{
    private Environment $twigSandbox;

    public function __construct(
        private TwigContextBuilderService $twigContextBuilderService,
        private MjmlBodyParserService $mjmlBodyParserService,
    ) {
        $this->twigSandbox = $this->createSandbox();
    }

    public function supports(string $extension): bool
    {
        return str_contains($extension, 'twig');
    }

    private function createSandbox(): Environment
    {
        $allowedTags = ['if', 'for', 'set'];
        $allowedFilters = ['escape', 'upper', 'lower', 'length', 'format_datetime', 'date', 'round'];
        $allowedFunctions = [];
        $allowedMethods = [];
        $allowedProperties = [];

        $policy = new SecurityPolicy(
            $allowedTags,
            $allowedFilters,
            $allowedFunctions,
            $allowedMethods,
            $allowedProperties
        );

        $loader = new ArrayLoader();
        $twigSandbox = new Environment($loader);
        $twigSandbox->addExtension(new SandboxExtension($policy, true));

        return $twigSandbox;
    }

    public function parseTemplate(string $templateContent, array $variables = []): string
    {
        try {
            $template = $this->twigSandbox->createTemplate($templateContent);
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
        catch (SecurityError $e) {
            throw new ParserException($e->getMessage(), 400);
        }
    }
}
