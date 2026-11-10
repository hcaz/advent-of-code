<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2024;

use Aoc\Result;
use Aoc\Solution;
use Illuminate\Support\Collection;

/**
 * 2024 day 1: Historian Hysteria.
 *
 * Step logic is the original, unchanged -- including sorting then popping both
 * lists in step, and the countBy() closure whose true/false keys become 0/1.
 */
final class Day01 implements Solution
{
    private Collection $listA;

    private Collection $listB;

    public function partOne(string $input): Result
    {
        $this->loadData($input);

        $pairs = $this->listA->count();

        $listA = $this->listA->sort();
        $listB = $this->listB->sort();

        $count = 0;
        while ($listA->count() > 0) {
            $a = $listA->pop();
            $b = $listB->pop();
            $count += abs($a - $b);
        }

        return Result::of($count, ['pairs' => $pairs]);
    }

    public function partTwo(string $input): Result
    {
        $this->loadData($input);

        $pairs = $this->listA->count();

        $listA = $this->listA->sort();
        $listB = $this->listB->sort();

        $count = 0;
        $matched = 0;
        while ($listA->count() > 0) {
            $a = $listA->pop();
            $times = $listB->countBy(function ($value) use ($a) {
                return $value == $a;
            });
            if (isset($times[1])) {
                $count += ($a * $times[1]);
                $matched++;
            }
        }

        return Result::of($count, ['pairs' => $pairs, 'matched' => $matched]);
    }

    private function loadData(string $input): void
    {
        $this->listA = collect([]);
        $this->listB = collect([]);

        foreach (explode("\n", $input) as $line) {
            $data = explode('   ', $line);
            if (count($data) > 1) {
                $this->listA->add($data[0]);
                $this->listB->add($data[1]);
            }
        }
    }
}
