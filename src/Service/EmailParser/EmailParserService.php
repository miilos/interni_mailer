<?php

namespace App\Service\EmailParser;

use App\Dto\EmailDto;
use App\Service\EmailParser\BodyParser\BodyParserService;
use App\Service\EmailParser\BodyParser\MjmlBodyParserService;
use App\Service\EmailParser\BodyParser\TwigBodyParserService;

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

        if ($emailDto->getBodyTemplate() && !$emailDto->getBody()) {
            $template = $this->bodyTemplateResolver->resolve($emailDto->getBodyTemplate());
            $body = $template->getContent();
            $body = $this->bodyParser->parse($body, $template->getExtension(), $template->getVariables());

            $emailDto->setBody($body);
        }

        $emailDto->setBody(
            $this->bodyParser->parse($emailDto->getBody(), 'html.twig', $emailDto->getVariables())
        );

        $emailDto->setBody(
            $this->variableParser->parseVariables($emailDto->getBody())
        );

        $emailDto->setTo(
            $this->groupResolver->resolveGroupAddresses($emailDto->getTo())
        );

        return $emailDto;
    }
}
