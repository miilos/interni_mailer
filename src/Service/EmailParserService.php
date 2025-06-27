<?php

namespace App\Service;

use App\Dto\EmailDto;

class EmailParserService
{
    public function __construct(
        private EmailVariableParserService $variableParser,
        private EmailBodyTemplateResolverService $bodyTemplateResolver,
        private GroupResolverService $groupResolver
    ) {}

    public function parse(EmailDto $emailDto): EmailDto
    {
        $emailDto->setSubject(
            $this->variableParser->parseVariables($emailDto->getSubject())
        );

        if ($emailDto->getBodyTemplate()) {
            $emailDto->setBody(
                $this->bodyTemplateResolver->resolve($emailDto->getBodyTemplate())
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
}
