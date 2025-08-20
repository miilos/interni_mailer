<?php

namespace App\Service;

use Sabberworm\CSS\Parser;

class CssSanitizerService
{
    private array $allowedProperties;

    public function __construct(array $allowedProperties)
    {
        $this->allowedProperties = $allowedProperties;
    }

    public function sanitize(string $html): string
    {
        // find all style tags and replace them
        return preg_replace_callback(
            '/<style\b[^>]*>(.*?)<\/style>/is',
            function ($matches) {
                $cleanCss = $this->sanitizeCss($matches[1]);
                return "<style>{$cleanCss}</style>";
            },
            $html
        );
    }

    public function sanitizeCss(string $css): string
    {
        $parser = new Parser($css);
        try {
            $cssDocument = $parser->parse();
        } catch (\Exception $e) {
            // remove the style tag if there's any malformed css
            return '';
        }

        foreach ($cssDocument->getAllDeclarationBlocks() as $block) {
            foreach ($block->getRules() as $rule) {
                $property = $rule->getRule();

                if (!in_array($property, $this->allowedProperties)) {
                    $block->removeRule($rule);
                }
            }
        }

        return $cssDocument->render();
    }
}
