<?php

namespace App\Service\EmailParser;

use App\Repository\EmailBodyRepository;
use App\Repository\EmailVariableRepository;

class EmailVariableParserService
{
    private array $variables;

    public function __construct(
        private EmailBodyRepository $emailBodyRepository,
        private EmailVariableRepository $emailVariableRepository,
    ) {
        $this->variables = $this->emailVariableRepository->findAll();
    }

    public function parseBodyTemplate(string $templateName): string
    {
        $body = $this->emailBodyRepository->findOneBy(['name' => $templateName])->getContent();

        return $body;
    }

    public function isHtml(string $content): bool
    {
        return preg_match('/<\s?[^\>]*\/?\s?>/i',  $content);
    }

    // gets only the names of all the variables in the given text,
    // so that their values can be fetched with fetchVariableValues()
    private function getVariableNames(string $text): array
    {
        // get all variables out of the given subject or body using a regex
        $matches = [];
        preg_match_all('/{{\s*([^}]+)\s*}}/i', $text, $matches, PREG_SET_ORDER);

        $varNames = [];
        // $match[0] = {{ var_name }}
        // $match[1] = var_name
        foreach ($matches as $match) {
            // trim any spaces between the curly braces and the variable name
            $varNames[] = trim($match[1]);
        }

        return  $varNames;
    }

    // gets the names of all the variables in the given text with getVariableNames(),
    // and then fetches their values from the database
    private function fetchVariableValues(string $text): array
    {
        $names = $this->getVariableNames($text);

        $variableValues = [];
        foreach ($names as $name) {
            $value = null;
            foreach ($this->variables as $variable) {
                if ($variable->getName() === $name) {
                    $value = $variable->getValue();
                }
            }

            if ($value) {
                $variableValues[$name] = $value;
            }
        }

        return $variableValues;
    }

    // gets the name value pairs from fetchVariableValues and replaces the placeholders with actual values
    public function parseVariables(string $text): string
    {
        $variables = $this->fetchVariableValues($text);

        foreach ($variables as $name => $value) {
            // allows for variable syntax to support {{name}}, {{ name }}, {{    name    }}...
            $pattern = '/{{\s*' . preg_quote($name, '/') . '\s*}}/';
            $text = preg_replace($pattern, $value, $text);
        }

        return $text;
    }
}
