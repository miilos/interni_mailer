<?php

namespace App\Service;

use App\Dto\EmailTemplateDto;
use App\Entity\EmailTemplate;
use App\Repository\EmailBodyRepository;
use Faker\Factory;
use http\Exception\InvalidArgumentException;

class EmailTemplateCreatorService
{
    public function __construct(
        private EmailBodyRepository $emailBodyRepository,
    ) {}

    public function createTemplateWithBodyName(EmailTemplateDto $emailTemplateDto): EmailTemplate
    {
        $bodyTemplate = $this->emailBodyRepository->getBodyTemplateByName($emailTemplateDto->getBodyTemplateName());

        if (!$bodyTemplate) {
            throw new InvalidArgumentException('No email body template found with that name!');
        }

        $template = new EmailTemplate();

        if ($emailTemplateDto->getName()) {
            $template->setName($emailTemplateDto->getName());
        }
        else {
            $faker = Factory::create('en_US');
            $name = implode('-', $faker->words(2)) . '-' . time();
            $template->setName($name);
        }

        $template->setSubject($emailTemplateDto->getSubject());
        $template->setFromAddr($emailTemplateDto->getFrom());
        $template->setToAddr($emailTemplateDto->getTo());
        $template->setCc($emailTemplateDto->getCc());
        $template->setBcc($emailTemplateDto->getBcc());
        $template->setBody($emailTemplateDto->getBody());
        $template->setCreatedAt(new \DateTimeImmutable());
        $template->setBodyTemplateName($emailTemplateDto->getBodyTemplateName());
        $template->setBodyTemplate($bodyTemplate);

        return $template;
    }
}
