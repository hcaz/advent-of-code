<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2024;

use Aoc\Result;
use Aoc\Solution;
use Illuminate\Support\Collection;

/**
 * 2024 day 2: Red-Nosed Reports.
 *
 * Step logic is the original, unchanged: count problematic levels per report
 * and treat the report as safe when that count is within the allowance.
 *
 * NOTE: part two does not agree with the recorded answer. This code produces
 * 385; the recorded answer, which Advent of Code accepted, is 381. Part one
 * agrees exactly (326). The puzzle's stated rule is "remove a single level and
 * re-check", which this allowance-based approach approximates, so the two
 * diverge on 4 reports. The recorded answer is left alone and the regression
 * suite records this as a known divergence rather than hiding it.
 */
final class Day02 implements Solution
{
    private Collection $reports;

    public function partOne(string $input): Result
    {
        $this->loadData($input);

        return $this->processReports(0);
    }

    public function partTwo(string $input): Result
    {
        $this->loadData($input);

        return $this->processReports(1);
    }

    private function processReports($allowedLevelIssues = 0): Result
    {
        $safeReports = 0;
        $unsafeReports = 0;

        while ($this->reports->count() > 0) {
            $report = $this->reports->pop();
            $lastNumber = null;
            $increasing = null;
            $badNumbers = 0;
            foreach ($report as $number) {
                if ($lastNumber == $number) {
                    $badNumbers++;
                } elseif (! is_null($lastNumber)) {
                    $diff = intval($lastNumber) - intval($number);

                    if (is_null($increasing)) {
                        $increasing = $diff > 0;
                    }

                    if (abs($diff) > 3) {
                        $badNumbers++;
                    } elseif (($increasing && $diff < 0) || (! $increasing && $diff > 0)) {
                        $badNumbers++;
                    }
                }

                $lastNumber = $number;
            }

            if ($badNumbers > $allowedLevelIssues) {
                $unsafeReports++;
            } else {
                $safeReports++;
            }
        }

        return Result::of($safeReports, [
            'reports' => $safeReports + $unsafeReports,
            'unsafe' => $unsafeReports,
            'allowed issues' => $allowedLevelIssues,
        ]);
    }

    private function loadData(string $input): void
    {
        $this->reports = collect([]);

        foreach (explode("\n", $input) as $line) {
            $data = explode(' ', $line);
            if (count($data) > 0 && ! empty($data[0])) {
                $this->reports->add($data);
            }
        }
    }
}
