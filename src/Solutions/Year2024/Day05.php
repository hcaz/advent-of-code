<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2024;

use Aoc\Result;
use Aoc\Solution;

/**
 * 2024 day 5: Print Queue.
 *
 * Part two repeatedly swaps the first offending pair and re-checks until no
 * rule is broken -- a bubble sort driven by the rule list. Kept as-is because
 * it is what produced the recorded answer.
 */
final class Day05 implements Solution
{
    public function partOne(string $input): Result
    {
        [$rules, $updates] = $this->parse($input);

        $sum = 0;
        $ordered = 0;

        foreach ($updates as $update) {
            if ($this->firstBrokenRule($rules, $update) === null) {
                $ordered++;
                $sum += (int) $update[intdiv(count($update), 2)];
            }
        }

        return Result::of($sum, [
            'updates' => count($updates),
            'rules' => count($rules),
            'already ordered' => $ordered,
        ]);
    }

    public function partTwo(string $input): Result
    {
        [$rules, $updates] = $this->parse($input);

        $sum = 0;
        $fixed = 0;

        foreach ($updates as $update) {
            if ($this->firstBrokenRule($rules, $update) === null) {
                continue;
            }

            $fixed++;

            while (($broken = $this->firstBrokenRule($rules, $update)) !== null) {
                [$i, $j] = $broken;
                [$update[$i], $update[$j]] = [$update[$j], $update[$i]];
            }

            $sum += (int) $update[intdiv(count($update), 2)];
        }

        return Result::of($sum, [
            'updates' => count($updates),
            'rules' => count($rules),
            'reordered' => $fixed,
        ]);
    }

    /**
     * Positions of the first pair that violates a rule, or null if ordered.
     *
     * @param  list<array{string, string}>  $rules
     * @param  list<string>  $update
     * @return array{int, int}|null
     */
    private function firstBrokenRule(array $rules, array $update): ?array
    {
        $position = array_flip($update);

        foreach ($rules as [$before, $after]) {
            if (! isset($position[$before], $position[$after])) {
                continue;
            }

            if ($position[$before] > $position[$after]) {
                return [$position[$before], $position[$after]];
            }
        }

        return null;
    }

    /** @return array{list<array{string, string}>, list<list<string>>} */
    private function parse(string $input): array
    {
        $rules = [];
        $updates = [];
        $readingRules = true;

        foreach (explode("\n", $input) as $line) {
            $line = trim($line);

            if ($line === '') {
                $readingRules = false;

                continue;
            }

            if ($readingRules) {
                $pair = explode('|', $line);

                if (count($pair) === 2) {
                    $rules[] = [$pair[0], $pair[1]];
                }

                continue;
            }

            $updates[] = explode(',', $line);
        }

        return [$rules, $updates];
    }
}
