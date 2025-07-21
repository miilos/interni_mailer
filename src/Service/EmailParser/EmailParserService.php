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
    ) {}

    /**
     * @param EmailDto $emailDto
     * @return EmailDto[]
     * @throws \Random\RandomException
     */
    public function parse(EmailDto $emailDto): array
    {
        $emailDto->setSubject(
            $this->variableParser->parseVariables($emailDto->getSubject())
        );

        $addresses = $this->groupResolver->resolveGroupAddresses($emailDto->getTo());
        $resDtos = [];

        // take the input dto and create a separate dto for each of the recipient email addresses.
        // this is done so that each email can be sent and logged separately, in case only one of them fails sending or something similar happens

        foreach ($addresses as $address) {
            $singleAddressEmailDto = clone $emailDto;

            $singleAddressEmailDto->setId(bin2hex(random_bytes(8)));

            $singleAddressEmailDto->setTo($address);

            if ($singleAddressEmailDto->getBodyTemplate()) {
                $template = $this->bodyTemplateResolver->resolve($singleAddressEmailDto->getBodyTemplate());
                $body = $template['content'];

                if ($template['extension'] === 'html.twig') {
                    $body = $this->twigBodyParser->renderTemplate($body, $singleAddressEmailDto);
                }

                $singleAddressEmailDto->setBody($body);
            }

            $singleAddressEmailDto->setBody(
                $this->variableParser->parseVariables($singleAddressEmailDto->getBody())
            );

            $resDtos[] = $singleAddressEmailDto;
        }

        return $resDtos;
    }
}
