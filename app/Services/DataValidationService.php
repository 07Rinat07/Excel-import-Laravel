<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;

class DataValidationService
{
    /**
     * Build validation rules from template column validation_rules
     */
    public function buildValidationRules(array $validationRules): array|string
    {
        if (empty($validationRules)) {
            return '';
        }

        $rules = [];

        foreach ($validationRules as $rule) {
            if (is_string($rule)) {
                $rules[] = trim($rule);
            } elseif (is_array($rule) && isset($rule['rule'])) {
                $rules[] = trim($rule['rule']);
            }
        }

        $rules = array_values(array_filter($rules, fn ($rule) => $rule !== ''));

        return implode('|', $rules);
    }

    /**
     * Validate a single row of data against template column rules
     *
     * @param array $rowData Key-value pairs of column key => value
     * @param array $templateColumns Array of template columns with validation_rules
     * @return array ['valid' => bool, 'errors' => array of error messages]
     */
    public function validateRow(array $rowData, array $templateColumns): array
    {
        $rules = [];
        $messages = [];

        foreach ($templateColumns as $column) {
            $key = $column['key'];

            if (!empty($column['validation_rules'])) {
                $rawRules = $column['validation_rules'];
                if (is_string($rawRules)) {
                    $rawRules = $this->parseRuleString($rawRules);
                }
                $rulesForColumn = $this->buildValidationRules((array) $rawRules);
                if ($rulesForColumn) {
                    $rules[$key] = $rulesForColumn;

                    // Add custom error messages
                    $messages[$key . '.required'] = "{$column['label']} is required";
                    $messages[$key . '.email'] = "{$column['label']} must be a valid email";
                    $messages[$key . '.unique'] = "{$column['label']} value already exists";
                    $messages[$key . '.regex'] = "{$column['label']} format is invalid";
                }
            }
        }

        if (empty($rules)) {
            return ['valid' => true, 'errors' => []];
        }

        $validator = Validator::make($rowData, $rules, $messages);

        return [
            'valid' => $validator->passes(),
            'errors' => $validator->errors()->toArray(),
        ];
    }

    /**
     * Parse a rule string into array parts.
     */
    private function parseRuleString(string $rules): array
    {
        $rules = trim($rules);
        if ($rules === '') {
            return [];
        }

        if (str_starts_with($rules, '[')) {
            $decoded = json_decode($rules, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        if (str_contains($rules, "\n")) {
            return array_values(array_filter(array_map('trim', preg_split("/\r\n|\n|\r/", $rules))));
        }

        return array_values(array_filter(array_map('trim', explode('|', $rules))));
    }

    /**
     * Validate multiple rows
     */
    public function validateRows(array $rowsData, array $templateColumns): array
    {
        $results = [];
        $failedRows = [];

        foreach ($rowsData as $rowIndex => $rowData) {
            $validation = $this->validateRow($rowData, $templateColumns);

            if (!$validation['valid']) {
                $failedRows[] = [
                    'row_index' => $rowIndex,
                    'data' => $rowData,
                    'errors' => $validation['errors'],
                ];
            } else {
                $results[$rowIndex] = $rowData;
            }
        }

        return [
            'valid_rows' => $results,
            'failed_rows' => $failedRows,
            'summary' => [
                'total' => count($rowsData),
                'valid' => count($results),
                'failed' => count($failedRows),
                'success_rate' => count($rowsData) > 0
                    ? round((count($results) / count($rowsData)) * 100, 2)
                    : 0,
            ],
        ];
    }
}
