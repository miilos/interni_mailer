<?php

namespace App\Service\EmailParser\BodyParser;

use App\Service\EmailParser\BodyParser\ParserException;
use Symfony\Component\HttpClient\HttpClient;

class MjmlBodyParserService implements BodyParserInterface
{
    public function supports(string $extension): bool
    {
        return str_contains($extension, 'mjml');
    }

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
