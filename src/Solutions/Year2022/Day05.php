<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2022;

use Aoc\Result;
use Aoc\Solution;
use Illuminate\Support\Collection;

/**
 * 2022 day 5: Supply Stacks.
 *
 * Step logic is the original, unchanged. The only thing dropped is the
 * displayStacks() helper, which printed the parsed crate stacks to the
 * terminal -- that is the puzzle input in another shape, and notes here are
 * committed to a publicly mirrored repo.
 */
final class Day05 implements Solution
{
    private Collection $stacks;

    private Collection $moves;

    public function partOne(string $input): Result
    {
        $this->loadData($input);

        foreach ($this->moves as $move) {
            for ($i = 0; $i < $move['amount']; $i++) {
                $crate = $this->stacks[$move['from'] - 1]->pop();
                $this->stacks[$move['to'] - 1]->push($crate);
            }
        }

        $stackCount = $this->stacks->count();
        $moveCount = $this->moves->count();

        $answer = $this->stacks->map(function ($stack) {
            return $stack->pop();
        })->implode('');

        return Result::of($answer, ['stacks' => $stackCount, 'moves' => $moveCount]);
    }

    public function partTwo(string $input): Result
    {
        $this->loadData($input);

        foreach ($this->moves as $move) {
            $crates = $this->stacks[$move['from'] - 1]->pop($move['amount']);
            foreach ($crates->reverse() as $crate) {
                $this->stacks[$move['to'] - 1]->push($crate);
            }
        }

        $stackCount = $this->stacks->count();
        $moveCount = $this->moves->count();

        $answer = $this->stacks->map(function ($stack) {
            return $stack->pop();
        })->implode('');

        return Result::of($answer, ['stacks' => $stackCount, 'moves' => $moveCount]);
    }

    private function loadData(string $input): void
    {
        $this->stacks = collect([]);
        $this->moves = collect([]);
        $stacks = true;
        foreach (explode("\n", $input) as $line) {
            if ($line == '') {
                $stacks = false;

                continue;
            }

            if ($stacks) {
                if (! str_contains($line, '[')) {
                    continue;
                }

                $stackCount = round(strlen($line) / 4);
                for ($i = 0; $i < $stackCount; $i++) {
                    if (! isset($this->stacks[$i])) {
                        $this->stacks[$i] = collect([]);
                    }
                    $crate = substr($line, ($i * 4) + 1, 1);
                    if (! empty(trim($crate))) {
                        $this->stacks[$i]->prepend($crate);
                    }
                }
            } else {
                $move = explode(' ', $line);
                $this->moves->push([
                    'amount' => $move[1],
                    'from' => $move[3],
                    'to' => $move[5],
                ]);
            }
        }
    }
}
