<?php

namespace App\Controller;

use App\Command\AssembleTemplateCommand;
use App\Dto\EmailDto;
use App\Event\SendEmailEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class EmailController extends AbstractController
{
    #[Route('/api/send', methods: ['POST'])]
    public function send(
        EventDispatcherInterface $dispatcher,
        #[MapRequestPayload]
        EmailDto $emailDto,
    ): JsonResponse
    {
        $dispatcher->dispatch(new SendEmailEvent($emailDto));

        return $this->json([
            'status' => 'success',
            'message' => 'email sent!'
        ]);
    }

    #[Route('/api/{templateName}/send', methods: ['POST'])]
    public function sendFromTemplate(
        string $templateName
    ): JsonResponse
    {
        // use EmailTemplateAssemblerService to create email from template and dispatch the send event

        return $this->json([
            'status' => 'success',
            'message' => 'email sent!'
        ]);
    }
}
