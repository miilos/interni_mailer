<?php

namespace App\Validator\SupportedBodyFileExtension;

enum SupportedBodyFileExtensionsEnum: string
{
    case EXTENSIONS_TXT = 'txt';
    case EXTENSIONS_HTML = 'html';
    case EXTENSIONS_TWIG = 'html.twig';
}
