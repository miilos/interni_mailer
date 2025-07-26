<?php

namespace App\Service\EmailParser;

use Symfony\Component\HttpClient\HttpClient;

class MjmlBodyParserService implements BodyParserInterface
{
    public function parseTemplate(string $templateContent): string
    {
        $httpClient = HttpClient::create();

        $response = $httpClient->request('POST', 'http://mjml:3000/parse', [
            'json' => [
                'mjml' => $templateContent,
            ],
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);

        $content = $response->toArray(false);
        $statusCode = $response->getStatusCode();

        if ($statusCode >= 400) {
            throw new ParserException($content['message'], $statusCode);
        }

        return $content['data']['html'];
    }
}
