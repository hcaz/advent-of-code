<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2024;

use Aoc\Result;
use Aoc\Solution;
use Illuminate\Support\Collection;

/**
 * 2024 day 5: Print Queue.
 *
 * Step logic is the original, unchanged -- including part two swapping the
 * first offending pair and re-checking until no rule is broken.
 *
 * The one edit: the middle index is cast with (int) rather than left as a float
 * for PHP to convert implicitly. PHP truncates toward zero, which is exactly
 * what the cast does, so the behaviour is identical -- it only silences the
 * deprecation notice PHP 8.1 added for implicit float array keys.
 */
final class Day05 implements Solution
{
    private Collection $rules;

    private Collection $updates;

    public function partOne(string $input): Result
    {
        $this->loadData($input);

        $subOfMiddleNumbersInOrder = 0;
        $ordered = 0;

        foreach ($this->updates as $update) {
            if (is_null($this->checkRules($update))) {
                $ordered++;
                $indexOfMiddle = $update->get((int) ($update->count() / 2));
                $subOfMiddleNumbersInOrder += $indexOfMiddle;
            }
        }

        return Result::of($subOfMiddleNumbersInOrder, [
            'updates' => $this->updates->count(),
            'rules' => $this->rules->count(),
            'already ordered' => $ordered,
        ]);
    }

    public function partTwo(string $input): Result
    {
        $this->loadData($input);

        $subOfMiddleNumbersInOrder = 0;
        $reordered = 0;

        foreach ($this->updates as $update) {
            $brokenRules = $this->checkRules($update);
            if (! is_null($brokenRules)) {
                $reordered++;
                while (! is_null($brokenRules)) {
                    $firstNumber = $update->get($brokenRules[0]);
                    $secondNumber = $update->get($brokenRules[1]);
                    $update->forget($brokenRules[0]);
                    $update->forget($brokenRules[1]);
                    $update->put($brokenRules[0], $secondNumber);
                    $update->put($brokenRules[1], $firstNumber);

                    $brokenRules = $this->checkRules($update);
                }

                $indexOfMiddle = $update->get((int) ($update->count() / 2));
                $subOfMiddleNumbersInOrder += $indexOfMiddle;
            }
        }

        return Result::of($subOfMiddleNumbersInOrder, [
            'updates' => $this->updates->count(),
            'rules' => $this->rules->count(),
            'reordered' => $reordered,
        ]);
    }

    private function checkRules($update)
    {
        foreach ($this->rules as $rule) {
            if ($update->contains($rule[0]) && $update->contains($rule[1])) {
                $index0 = $update->search($rule[0]);
                $index1 = $update->search($rule[1]);
                if ($index0 > $index1) {
                    return [$index0, $index1];
                }
            }
        }

        return null;
    }

    private function loadData(string $input): void
    {
        $this->rules = collect([]);
        $this->updates = collect([]);

        $loadingRules = true;
        foreach (explode("\n", $input) as $line) {
            if ($line == '') {
                $loadingRules = false;

                continue;
            }

            if ($loadingRules) {
                $this->rules->add(collect(explode('|', $line)));
            } else {
                $this->updates->add(collect(explode(',', $line)));
            }
        }
    }
}
