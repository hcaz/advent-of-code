<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2024;

use Aoc\Result;
use Aoc\Solution;

/**
 * 2024 day 10: Hoof It.
 *
 * Step logic is the original, unchanged -- including the four separate
 * direction blocks in calculateTrailHeadScore() and the ignoreDuplicateRoutes
 * flag that switches part two from counting summits to counting routes.
 */
final class Day10 implements Solution
{
    /** @var list<list<string>> */
    private array $data = [];

    public function partOne(string $input): Result
    {
        $this->loadData($input);

        $sumOfTrailHeadScores = 0;
        $numberOfTrailHeads = 0;

        foreach ($this->data as $rowIndex => $line) {
            foreach ($line as $colIndex => $char) {
                if ($char === '0') {
                    $foundTrailEnds = $this->calculateTrailHeadScore($rowIndex, $colIndex);
                    $score = count($foundTrailEnds);
                    if ($score > 0) {
                        $numberOfTrailHeads++;
                        $sumOfTrailHeadScores += $score;
                    }
                }
            }
        }

        return Result::of($sumOfTrailHeadScores, [
            'grid' => count($this->data).'x'.count($this->data[0]),
            'trailheads' => $numberOfTrailHeads,
        ]);
    }

    public function partTwo(string $input): Result
    {
        $this->loadData($input);

        $sumOfTrailHeadScores = 0;
        $numberOfTrailHeads = 0;

        foreach ($this->data as $rowIndex => $line) {
            foreach ($line as $colIndex => $char) {
                if ($char === '0') {
                    $foundTrailEnds = $this->calculateTrailHeadScore($rowIndex, $colIndex, ignoreDuplicateRoutes: true);
                    $score = count($foundTrailEnds);
                    if ($score > 0) {
                        $numberOfTrailHeads++;
                        $sumOfTrailHeadScores += array_sum($foundTrailEnds);
                    }
                }
            }
        }

        return Result::of($sumOfTrailHeadScores, [
            'grid' => count($this->data).'x'.count($this->data[0]),
            'trailheads' => $numberOfTrailHeads,
        ]);
    }

    private function calculateTrailHeadScore($rowIndex, $colIndex, $value = 1, $foundTrailEnds = [], $ignoreDuplicateRoutes = false): array
    {
        if (isset($this->data[$rowIndex][$colIndex + 1])) {
            if (intval($this->data[$rowIndex][$colIndex + 1]) == 9 && $value == 9 && (! isset($foundTrailEnds["$rowIndex-".($colIndex + 1)]) || $ignoreDuplicateRoutes)) {
                if (! isset($foundTrailEnds["$rowIndex-".($colIndex + 1)])) {
                    $foundTrailEnds["$rowIndex-".($colIndex + 1)] = 1;
                } else {
                    $foundTrailEnds["$rowIndex-".($colIndex + 1)]++;
                }
            } elseif (intval($this->data[$rowIndex][$colIndex + 1]) == $value) {
                $foundTrailEnds = array_merge($foundTrailEnds, $this->calculateTrailHeadScore($rowIndex, $colIndex + 1, value: $value + 1, foundTrailEnds: $foundTrailEnds, ignoreDuplicateRoutes: $ignoreDuplicateRoutes));
            }
        }
        if (isset($this->data[$rowIndex][$colIndex - 1])) {
            if (intval($this->data[$rowIndex][$colIndex - 1]) == 9 && $value == 9 && (! isset($foundTrailEnds["$rowIndex-".($colIndex - 1)]) || $ignoreDuplicateRoutes)) {
                if (! isset($foundTrailEnds["$rowIndex-".($colIndex - 1)])) {
                    $foundTrailEnds["$rowIndex-".($colIndex - 1)] = 1;
                } else {
                    $foundTrailEnds["$rowIndex-".($colIndex - 1)]++;
                }
            } elseif (intval($this->data[$rowIndex][$colIndex - 1]) == $value) {
                $foundTrailEnds = array_merge($foundTrailEnds, $this->calculateTrailHeadScore($rowIndex, $colIndex - 1, value: $value + 1, foundTrailEnds: $foundTrailEnds, ignoreDuplicateRoutes: $ignoreDuplicateRoutes));
            }
        }

        if (isset($this->data[$rowIndex + 1][$colIndex])) {
            if (intval($this->data[$rowIndex + 1][$colIndex]) == 9 && $value == 9 && (! isset($foundTrailEnds[($rowIndex + 1).'-'.$colIndex]) || $ignoreDuplicateRoutes)) {
                if (! isset($foundTrailEnds[($rowIndex + 1).'-'.$colIndex])) {
                    $foundTrailEnds[($rowIndex + 1).'-'.$colIndex] = 1;
                } else {
                    $foundTrailEnds[($rowIndex + 1).'-'.$colIndex]++;
                }
            } elseif (intval($this->data[$rowIndex + 1][$colIndex]) == $value) {
                $foundTrailEnds = array_merge($foundTrailEnds, $this->calculateTrailHeadScore($rowIndex + 1, $colIndex, value: $value + 1, foundTrailEnds: $foundTrailEnds, ignoreDuplicateRoutes: $ignoreDuplicateRoutes));
            }
        }
        if (isset($this->data[$rowIndex - 1][$colIndex])) {
            if (intval($this->data[$rowIndex - 1][$colIndex]) == 9 && $value == 9 && (! isset($foundTrailEnds[($rowIndex - 1).'-'.$colIndex]) || $ignoreDuplicateRoutes)) {
                if (! isset($foundTrailEnds[($rowIndex - 1).'-'.$colIndex])) {
                    $foundTrailEnds[($rowIndex - 1).'-'.$colIndex] = 1;
                } else {
                    $foundTrailEnds[($rowIndex - 1).'-'.$colIndex]++;
                }
            } elseif (intval($this->data[$rowIndex - 1][$colIndex]) == $value) {
                $foundTrailEnds = array_merge($foundTrailEnds, $this->calculateTrailHeadScore($rowIndex - 1, $colIndex, value: $value + 1, foundTrailEnds: $foundTrailEnds, ignoreDuplicateRoutes: $ignoreDuplicateRoutes));
            }
        }

        return $foundTrailEnds;
    }

    private function loadData(string $input): void
    {
        $this->data = [];
        $lines = explode("\n", $input);
        foreach ($lines as $lineData) {
            if (empty($lineData)) {
                continue;
            }
            $line = [];
            for ($i = 0; $i < strlen($lineData); $i++) {
                $line[] = $lineData[$i];
            }
            $this->data[] = $line;
        }
    }
}
