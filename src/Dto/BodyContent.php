<?php

namespace App\Dto;

class BodyContent
{
    private function __construct(
        private string $content,
        private bool $usesTemplate
    ) {}

    public static function plain(string $content): self
    {
        return new self($content, false);
    }

    public static function template(string $templateName): self
    {
        return new self($templateName, true);
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function usesTemplate(): bool
    {
        return $this->usesTemplate;
    }
}
