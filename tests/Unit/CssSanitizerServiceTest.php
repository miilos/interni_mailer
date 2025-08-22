<?php

namespace App\Tests\Unit;

use App\Service\CssSanitizerService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CssSanitizerServiceTest extends TestCase
{
    public static function sanitizeCssProvider(): array
    {
        return [
            ['div { color: red; display: flex; }'],
            ['div { color: red; background: url(javascript:alert(123)) }'],
        ];
    }

    #[DataProvider('sanitizeCssProvider')]
    public function testSanitizesCss(string $css): void
    {
        $allowedProperties = [
            'color',
            'background',
        ];
        $sanitizer = new CssSanitizerService($allowedProperties);

        $sanitizedCss = $sanitizer->sanitizeCss($css);

        $properties = '';
        if (preg_match('/\{([^}]*)\}/s', $sanitizedCss, $matches)) {
            $properties = trim($matches[1]);
        }

        $this->assertSame('color: red;', $properties);
    }
}
