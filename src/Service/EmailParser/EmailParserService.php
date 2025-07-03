<?php

namespace App\Service\EmailParser;

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
        $emailDto->setId(bin2hex(random_bytes(8)));

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
