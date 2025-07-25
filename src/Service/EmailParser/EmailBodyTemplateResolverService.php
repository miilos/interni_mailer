<?php

namespace App\Service\EmailParser;

use App\Entity\EmailBody;
use App\Repository\EmailBodyRepository;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class EmailBodyTemplateResolverService
{
    public function __construct(
        private EmailBodyRepository $emailBodyRepository,
    ) {}

    public function resolve(string $templateName): array
    {
        $template = $this->findTemplate($templateName);

        if (!$template) {
            throw new BadRequestException(sprintf('No template "%s" found!', $templateName));
        }

        return [
            'content' => $template->getContent(),
            'extension' => $template->getExtension(),
        ];
    }

    private function findTemplate(string $templateName): ?EmailBody
    {
        return $this->emailBodyRepository->findOneBy(['name' => $templateName]);
    }
}
