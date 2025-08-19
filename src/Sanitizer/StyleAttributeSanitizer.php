<?php

namespace App\Sanitizer;

use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface;

// custom sanitizer meant for only keeping whitelisted css attributes in html style attributes
class StyleAttributeSanitizer implements AttributeSanitizerInterface
{
    private array $allowedProperties;

    public function __construct(array $allowedProperties)
    {
        $this->allowedProperties = array_map('strtolower', $allowedProperties);
    }

    public function getSupportedElements(): ?array
    {
        return ['*'];
    }

    public function getSupportedAttributes(): ?array
    {
        return ['style'];
    }

    public function sanitizeAttribute(string $element, string $attribute, string $value, HtmlSanitizerConfig $config): ?string
    {
        // only modify style attributes
        if (strtolower($attribute) !== 'style') {
            return $value;
        }

        // get all css key-value pairs without semicolons and spaces
        $declarations = explode(';', trim($value, ' \t\n'));
        $allowedDeclarations = [];

        // get the prop and the value, and if the prop is whitelisted, add it to the return array
        foreach ($declarations as $declaration) {
            $declaration = trim($declaration);
            if ($declaration === '') {
                continue;
            }

            $parts = explode(':', $declaration, 2);
            $prop = trim($parts[0] ?? '');
            $value = trim($parts[1] ?? '');

            if (!$prop || !$value) {
                continue;
            }

            if (in_array($prop, $this->allowedProperties)) {
                $allowedDeclarations[] = "$prop: $value";
            }
        }

        return $allowedDeclarations ? implode('; ', $allowedDeclarations) : null;
    }
}
