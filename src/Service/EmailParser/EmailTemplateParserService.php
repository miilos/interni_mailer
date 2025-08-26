<?php

namespace App\Service\EmailParser;

use App\Dto\EmailDto;
use App\Entity\EmailTemplate;
use App\Repository\EmailTemplateRepository;

class EmailTemplateParserService
{
    public function __construct(
        private EmailTemplateRepository $emailTemplateRepository,
    ) {}

    // check if the property is set on the dto or the template,
    // if it's set on the dto, leave the property as-is
    // if it's not set on the dto but is set on the template,
    // use the template property
    public function parse(EmailDto $emailDto): EmailDto
    {
        $template = $this->getTemplate($emailDto->getEmailTemplate());

        $emailDto->setSubject(
            $this->setProperty($emailDto->getSubject(), $template->getSubject())
        );
        $emailDto->setFrom(
            $this->setProperty($emailDto->getFrom(), $template->getFromAddr())
        );
        $emailDto->setTo(
            $this->setProperty($emailDto->getTo(), $template->getToAddr())
        );
        $emailDto->setCc(
            $this->setProperty($emailDto->getCc(), $template->getCc())
        );
        $emailDto->setBcc(
            $this->setProperty($emailDto->getBcc(), $template->getBcc())
        );
        $emailDto->setBody(
            $this->setProperty($emailDto->getBody(), $template->getBody())
        );
        $emailDto->setBodyTemplate(
            $this->setProperty($emailDto->getBodyTemplate(), $template->getBodyTemplateName())
        );

        // if the email template uses a body template, copy it's variables into the dto
        // so they can be parsed later
        if ($template->getBodyTemplateName()) {
            $emailDto->setVariables($template->getBodyTemplate()->getVariables());
        }

        return $emailDto;
    }

    private function getTemplate(string $templateName): EmailTemplate
    {
        return $this->emailTemplateRepository->findOneBy(['name' => $templateName]);
    }

    private function setProperty(mixed $emailDtoProp, mixed $emailTemplateProp): mixed
    {
        return $emailDtoProp ?? $emailTemplateProp;
    }
}
