<?php

namespace App\Controller;

use App\Service\OpenAiApiConsumerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class OpenAiApiController extends AbstractController
{
    #[Route('/api/prompt', methods: ['POST'])]
    public function prompt(
        DecoderInterface $decoder,
        Request $request,
        OpenAiApiConsumerService $openAiApiConsumer
    ): JsonResponse
    {
        $body = $decoder->decode($request->getContent(), 'json');
        $prompt = $body['prompt'];

        $response = $openAiApiConsumer->prompt($prompt);

        return $this->json([
            'status' => 'success',
            'data' => [
                'response' => $response,
            ]
        ]);
    }
}
