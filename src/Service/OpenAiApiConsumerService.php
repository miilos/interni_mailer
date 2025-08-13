<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

class OpenAiApiConsumerService
{
    public function prompt(string $prompt): string
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('POST', 'http://openai:4000/prompt', [
            'json' => [
                'prompt' => $prompt,
            ],
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);

        $resContent = $response->toArray(false);
        return $resContent['data']['response'];
    }
}
