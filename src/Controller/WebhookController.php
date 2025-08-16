<?php

namespace App\Controller;

use App\Message\UpdateEmailStatusFromWebhook;
use App\Service\WebhookParser\WebhookParserService;
use Gedmo\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class WebhookController extends AbstractController
{
    #[Route('/webhook', methods: ['POST'])]
    public function handleWebhook(
        DecoderInterface $decoder,
        Request $request,
        WebhookParserService $webhookParser,
        MessageBusInterface $messageBus,
    ): JsonResponse
    {
        $webhookContent = $decoder->decode($request->getContent(), 'json');

        $webhookDto = $webhookParser->parse($webhookContent);

        $messageBus->dispatch(new UpdateEmailStatusFromWebhook($webhookDto));

        return $this->json([
            'status' => 'success',
            'data' => [
                'webhook' => $webhookDto
            ]
        ]);
    }
}
