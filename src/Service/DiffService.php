<?php

namespace App\Service;

use App\Entity\EmailBody;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

class DiffService
{
    private Differ $differ;

    public function __construct() {
        $this->differ = new Differ(new UnifiedDiffOutputBuilder);
    }

    public function generateDiff(EmailBody $oldTemplate, EmailBody $newTemplate): array
    {
        $fields = [
            'name' => [
                'old' => $oldTemplate->getName(),
                'new' => $newTemplate->getName(),
            ],
            'content' => [
                'old' => $oldTemplate->getContent(),
                'new' => $newTemplate->getContent(),
            ],
            'variables' => [
                'old' => $oldTemplate->getVariables(),
                'new' => $newTemplate->getVariables(),
            ]
        ];

        $changes = [];
        foreach ($fields as $field => $value) {
            $oldValue = $value['old'] ?? '';
            $newValue = $value['new'] ?? '';

            $status = '';
            if (is_string($oldValue) && is_string($newValue)) {
                $status = $this->getChangeType($oldValue, $newValue);
            }
            elseif (is_array($oldValue) && is_array($newValue)) {
                $status = $this->getChangeTypeArray($oldValue, $newValue);
            }

            if ($oldValue !== $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                    'status' => $status,
                    'diff' => $this->differ->diff($oldValue, $newValue),
                ];
            }
        }

        return $changes;
    }

    private function getChangeType(string $oldVal, string $newVal): string
    {
        if (!$oldVal && $newVal) {
            return 'added';
        }

        if ($oldVal && !$newVal) {
            return 'removed';
        }

        return 'modified';
    }

    private function getChangeTypeArray(array $oldVal, array $newVal): string
    {
        if (count($oldVal) < count($newVal)) {
            return 'added';
        }

        if (count($oldVal) > count($newVal)) {
            return 'removed';
        }

        return 'modified';
    }
}
