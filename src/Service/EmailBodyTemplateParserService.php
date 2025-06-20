<?php

namespace App\Service;

use App\Repository\EmailBodyRepository;

class EmailBodyTemplateParserService
{
    public function __construct(
        private EmailBodyRepository $emailBodyRepository
    ) {}

    public function parseBodyTemplate(string $templateName): string
    {
        $body = $this->emailBodyRepository->findOneBy(['name' => $templateName])->getContent();

        return $body;
    }
}
