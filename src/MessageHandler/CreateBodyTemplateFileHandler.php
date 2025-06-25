<?php

namespace App\MessageHandler;

use App\Message\CreateBodyTemplateFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateBodyTemplateFileHandler
{
    public function __construct(
        private Filesystem $filesystem,
        private string $rootDir
    ) {}

    public function __invoke(CreateBodyTemplateFile $createBodyTemplateFile)
    {
        $rootBodyFilesDir = $this->rootDir.'/var/body_templates';

        if (!$this->filesystem->exists($rootBodyFilesDir)) {
            $this->filesystem->mkdir($rootBodyFilesDir);
        }

        $emailBodyDto = $createBodyTemplateFile->getEmailBody();
        $filePath = $rootBodyFilesDir.'/'.$emailBodyDto->getName().'.'.$emailBodyDto->getExtension();
        $this->filesystem->dumpFile($filePath, $emailBodyDto->getContent());
    }
}
