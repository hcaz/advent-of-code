<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2022;

use Aoc\Result;
use Aoc\Solution;
use Illuminate\Support\Collection;

/**
 * 2022 day 1: Calorie Counting.
 *
 * Step logic is the original, unchanged. The only thing dropped is the
 * number_format() applied to the value immediately before printing it -- that
 * is display formatting, and it would have produced "66,186" against the
 * recorded "66186".
 */
final class Day01 implements Solution
{
    private Collection $elves;

    public function partOne(string $input): Result
    {
        $this->loadData($input);

        $mostCaloriesElement = $this->elves->first();
        $mostCaloriesElf = $this->elves->search(function ($elf) use ($mostCaloriesElement) {
            return $elf === $mostCaloriesElement;
        });
        $mostCaloriesTotal = $this->elves[$mostCaloriesElf]->sum();
        $mostCaloriesItems = $this->elves[$mostCaloriesElf]->count();

        return Result::of($mostCaloriesTotal, [
            'elves' => $this->elves->count(),
            'items' => $mostCaloriesItems,
        ]);
    }

    public function partTwo(string $input): Result
    {
        $this->loadData($input);

        $mostCaloriesElements = $this->elves->take(3);
        $mostCaloriesTotalTop3 = 0;
        $mostCaloriesItemsTop3 = 0;
        foreach ($mostCaloriesElements as $mostCaloriesElement) {
            $mostCaloriesElf = $this->elves->search(function ($elf) use ($mostCaloriesElement) {
                return $elf === $mostCaloriesElement;
            });
            $mostCaloriesItems = $this->elves[$mostCaloriesElf]->count();

            $mostCaloriesTotalTop3 = $mostCaloriesTotalTop3 + $this->elves[$mostCaloriesElf]->sum();
            $mostCaloriesItemsTop3 = $mostCaloriesItemsTop3 + $mostCaloriesItems;
        }

        return Result::of($mostCaloriesTotalTop3, [
            'elves' => $this->elves->count(),
            'items' => $mostCaloriesItemsTop3,
        ]);
    }

    private function loadData(string $input): void
    {
        $this->elves = collect([]);
        $tmpElf = collect([]);
        foreach (explode("\n", $input) as $line) {
            if ($line == '') {
                $this->elves->push($tmpElf);
                $tmpElf = collect([]);
            } else {
                $tmpElf->push($line);
            }
        }

        $this->elves = $this->elves->sortByDesc(function ($elf) {
            return $elf->sum();
        });
    }
}
