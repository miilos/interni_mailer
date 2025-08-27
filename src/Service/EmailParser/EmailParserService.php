<?php

namespace App\Service\EmailParser;

use App\Dto\EmailDto;
use App\Service\EmailParser\BodyParser\BodyParserService;

class EmailParserService
{
    public function __construct(
        private EmailVariableParserService $variableParser,
        private EmailBodyTemplateResolverService $bodyTemplateResolver,
        private GroupResolverService $groupResolver,
        private BodyParserService $bodyParser,
    ) {}

    public function parse(EmailDto $emailDto): EmailDto
    {
        $emailDto->setSubject(
            $this->variableParser->parseVariables($emailDto->getSubject())
        );

        if (
            ($emailDto->getBodyTemplate() && !$emailDto->getBody()) ||
            ($emailDto->getBodyTemplate() && $emailDto->getEmailTemplate())
        ) {
            $template = $this->bodyTemplateResolver->resolve($emailDto->getBodyTemplate());
            $body = $template->getContent();

            $variables = $this->combineVariableArrays($template->getVariables(), $emailDto->getVariables());

            $body = $this->bodyParser->parse($body, $template->getExtension(), $variables);

            $emailDto->setBody($body);
        }
        else {
            $emailDto->setBody(
                $this->bodyParser->parse($emailDto->getBody(), 'html.twig', $emailDto->getVariables())
            );
        }

        $emailDto->setBody(
            $this->variableParser->parseVariables($emailDto->getBody())
        );

        $emailDto->setTo(
            $this->groupResolver->resolveGroupAddresses($emailDto->getTo())
        );

        return $emailDto;
    }

    // allow the user to specify one or all of the variables from the body template to override
    private function combineVariableArrays(array $templateVariables, array $dtoVariables): array
    {
        $variables = [];

        foreach ($templateVariables as $key => $value) {
            if (array_key_exists($key, $dtoVariables)) {
                $variables[$key] = $dtoVariables[$key];
                continue;
            }

            $variables[$key] = $value;
        }

        return $variables;
    }
}
