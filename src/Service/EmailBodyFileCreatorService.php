<?php

namespace App\Service;

use App\Dto\EmailBodyDto;
use Symfony\Component\Filesystem\Filesystem;

class EmailBodyFileCreatorService
{
    private string $rootBodyFilesDir;
    public function __construct(
        private Filesystem $filesystem,
        private string $rootDir
    ) {
        $this->rootBodyFilesDir = $this->rootDir . '/var/body_templates';

        $this->createRootBodyTemplatesDir();
    }

    private function createRootBodyTemplatesDir(): void
    {
        if (!$this->filesystem->exists($this->rootBodyFilesDir)) {
            $this->filesystem->mkdir($this->rootBodyFilesDir);
        }
    }

    public function createBodyTemplateFile(EmailBodyDto $emailBodyDto): void
    {
        $filePath = $this->rootBodyFilesDir.'/'.$emailBodyDto->getName().'.'.$emailBodyDto->getExtension();
        $this->filesystem->dumpFile($filePath, $emailBodyDto->getContent());
    }
}
