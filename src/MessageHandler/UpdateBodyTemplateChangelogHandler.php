<?php

namespace App\MessageHandler;

use App\Message\UpdateBodyTemplateChangelog;
use App\Repository\EmailBodyChangelogRepository;
use App\Repository\EmailBodyRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateBodyTemplateChangelogHandler
{
    public function __construct(
        private EmailBodyChangelogRepository $emailBodyChangelogRepository,
        private EmailBodyRepository $emailBodyRepository,
    ) {}

    public function __invoke(UpdateBodyTemplateChangelog $updateChangelog)
    {
        $updatedTemplate = $this->emailBodyRepository->find(['id' => $updateChangelog->getBodyTemplateId()]);
        $diff = $updateChangelog->getDiff();
        $user = $updateChangelog->getUser();

        $this->emailBodyChangelogRepository->createEmailBodyChangelog($updatedTemplate, $diff, $user);
    }
}
