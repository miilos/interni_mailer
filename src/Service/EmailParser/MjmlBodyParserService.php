<?php

namespace App\Service\EmailParser;

use Symfony\Component\Process\Process;

class MjmlBodyParserService implements BodyParserInterface
{
    public function parseTemplate(string $templateContent): string
    {
        $process = new Process([
            'mjml', '--stdin', '--stdout'
        ]);
        $process->setInput($templateContent);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException('MJML compilation error: ' . $process->getErrorOutput());
        }

        return $process->getOutput();
    }
}
