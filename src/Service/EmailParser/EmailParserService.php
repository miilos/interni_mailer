<?php

namespace App\Service\EmailParser;

use App\Dto\EmailDto;

class EmailParserService
{
    public function __construct(
        private EmailVariableParserService $variableParser,
        private EmailBodyTemplateResolverService $bodyTemplateResolver,
        private GroupResolverService $groupResolver,
        private TwigBodyParserService $twigBodyParser,
        private MjmlBodyParserService $mjmlBodyParser,
    ) {}

    public function parse(EmailDto $emailDto): EmailDto
    {
        $emailDto->setId(bin2hex(random_bytes(8)));

        $emailDto->setSubject(
            $this->variableParser->parseVariables($emailDto->getSubject())
        );

        if ($emailDto->getBodyTemplate()) {
            $template = $this->bodyTemplateResolver->resolve($emailDto->getBodyTemplate());
            $body = $template['content'];

            if ($template['extension'] === 'html.twig') {
                $body = $this->twigBodyParser->parseTemplate($body);
            }
            elseif ($template['extension'] === 'mjml') {
                $body = $this->mjmlBodyParser->parseTemplate($body);
            }

            $emailDto->setBody($body);
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
