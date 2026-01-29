<?php

namespace App\Services;

use App\Models\ExcelTemplateColumn;
use Illuminate\Support\Collection;

class SmartMappingService
{
    /**
     * Calculate Levenshtein distance between two strings
     */
    public function levenshteinDistance(string $str1, string $str2): int
    {
        $len1 = strlen($str1);
        $len2 = strlen($str2);

        if ($len1 === 0) {
            return $len2;
        }
        if ($len2 === 0) {
            return $len1;
        }

        // Create matrix
        $d = [];
        for ($i = 0; $i <= $len1; $i++) {
            $d[$i][0] = $i;
        }
        for ($j = 0; $j <= $len2; $j++) {
            $d[0][$j] = $j;
        }

        // Calculate distance
        for ($i = 1; $i <= $len1; $i++) {
            for ($j = 1; $j <= $len2; $j++) {
                $cost = ($str1[$i - 1] === $str2[$j - 1]) ? 0 : 1;

                $d[$i][$j] = min(
                    $d[$i - 1][$j] + 1,      // deletion
                    $d[$i][$j - 1] + 1,      // insertion
                    $d[$i - 1][$j - 1] + $cost // substitution
                );
            }
        }

        return $d[$len1][$len2];
    }

    /**
     * Calculate similarity ratio (0-1)
     */
    public function calculateSimilarity(string $str1, string $str2): float
    {
        $maxLen = max(strlen($str1), strlen($str2));
        if ($maxLen === 0) {
            return 1.0;
        }

        $distance = $this->levenshteinDistance(
            strtolower($str1),
            strtolower($str2)
        );

        return 1 - ($distance / $maxLen);
    }

    /**
     * Find best matching template columns for Excel headers
     *
     * @param array $excelHeaders Array of headers from Excel file
     * @param Collection $templateColumns Collection of ExcelTemplateColumn models
     * @param float $threshold Similarity threshold (0-1)
     * @return array Array of matched columns with similarity scores
     */
    public function findBestMatches(
        array $excelHeaders,
        Collection $templateColumns,
        float $threshold = 0.6
    ): array
    {
        $matches = [];

        foreach ($excelHeaders as $index => $excelHeader) {
            $bestMatch = null;
            $bestScore = 0;

            foreach ($templateColumns as $templateColumn) {
                // Check label similarity
                $labelSimilarity = $this->calculateSimilarity(
                    $excelHeader,
                    $templateColumn->label
                );

                // Check key similarity
                $keySimilarity = $this->calculateSimilarity(
                    $excelHeader,
                    $templateColumn->key
                );

                // Use the best similarity
                $similarity = max($labelSimilarity, $keySimilarity);

                // Only consider matches above threshold
                if ($similarity > $bestScore && $similarity >= $threshold) {
                    $bestScore = $similarity;
                    $bestMatch = [
                        'column_id' => $templateColumn->id,
                        'column_key' => $templateColumn->key,
                        'column_label' => $templateColumn->label,
                        'similarity' => round($similarity * 100, 2),
                    ];
                }
            }

            $matches[$index] = [
                'excel_header' => $excelHeader,
                'match' => $bestMatch,
                'is_matched' => $bestMatch !== null,
            ];
        }

        return $matches;
    }

    /**
     * Suggest mapping based on template and Excel headers
     */
    public function suggestMapping(
        array $excelHeaders,
        Collection $templateColumns
    ): array
    {
        $matches = $this->findBestMatches($excelHeaders, $templateColumns);

        $mapping = [];
        $usedColumns = [];

        // First pass: assign high-confidence matches
        foreach ($matches as $index => $matchData) {
            if (
                $matchData['is_matched'] &&
                $matchData['match']['similarity'] >= 80 &&
                !in_array($matchData['match']['column_id'], $usedColumns)
            ) {
                $mapping[$index] = $matchData['match']['column_id'];
                $usedColumns[] = $matchData['match']['column_id'];
            }
        }

        // Second pass: assign remaining matches
        foreach ($matches as $index => $matchData) {
            if (
                !isset($mapping[$index]) &&
                $matchData['is_matched'] &&
                !in_array($matchData['match']['column_id'], $usedColumns)
            ) {
                $mapping[$index] = $matchData['match']['column_id'];
                $usedColumns[] = $matchData['match']['column_id'];
            }
        }

        return [
            'mapping' => $mapping,
            'suggestions' => $matches,
            'confidence' => $this->calculateMappingConfidence($matches),
        ];
    }

    /**
     * Calculate overall confidence of mapping suggestions
     */
    private function calculateMappingConfidence(array $matches): array
    {
        $totalMatches = count($matches);
        $matchedCount = count(array_filter($matches, fn($m) => $m['is_matched']));
        $highConfidenceCount = count(array_filter(
            $matches,
            fn($m) => $m['is_matched'] && $m['match']['similarity'] >= 80
        ));

        return [
            'total_headers' => $totalMatches,
            'matched_headers' => $matchedCount,
            'high_confidence_matches' => $highConfidenceCount,
            'coverage_percentage' => round(($matchedCount / $totalMatches) * 100, 2),
            'confidence_percentage' => round(($highConfidenceCount / $totalMatches) * 100, 2),
        ];
    }
}
