<?php

namespace App\Service;

use App\Entity\EmailBody;
use App\Message\UpdateBodyTemplateChangelog;
use App\Repository\EmailBodyRepository;
use App\Service\EmailParser\BodyParser\BodyParserService;
use Symfony\Component\Messenger\MessageBusInterface;

class BodyTemplateUpdateService
{
    public function __construct(
        private BodyParserService $bodyParser,
        private DiffService $diffService,
        private MessageBusInterface $messageBus,
        private EmailBodyRepository $emailBodyRepository,
    ) {}

    public function updateTemplate(EmailBody $body, array $newData): ?EmailBody {
        $newBody = $this->getUpdatedBody($body, $newData);

        $diff = $this->diffService->generateDiff($body, $newBody);

        $updatedBody = $this->emailBodyRepository->updateBodyTemplate($body, $newBody);

        if ($diff) {
            $this->messageBus->dispatch(new UpdateBodyTemplateChangelog($updatedBody->getId(), $diff));
        }

        return $updatedBody;
    }

    private function getUpdatedBody(EmailBody $body, array $newData): EmailBody
    {
        $newBody = clone $body;

        $allowedFields = ['name', 'content', 'variables'];

        foreach ($newData as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $setter = 'set'.ucfirst($key);
                $newBody->$setter($value);
            }
        }

        $newBody->setParsedBodyHtml(
            $this->bodyParser->parse($newBody->getContent(), $newBody->getExtension(), $newBody->getVariables())
        );

        return $newBody;
    }
}
