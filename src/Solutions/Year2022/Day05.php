<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2022;

use Aoc\Result;
use Aoc\Solution;

/**
 * 2022 day 5: Supply Stacks.
 *
 * The original also printed the full parsed stacks to the terminal through a
 * displayStacks() helper. That is dropped rather than ported: the stacks are
 * the puzzle input in another shape, and notes here are committed to a repo
 * that is mirrored publicly. Only counts and the answer itself are recorded.
 */
final class Day05 implements Solution
{
    public function partOne(string $input): Result
    {
        [$stacks, $moves] = $this->parse($input);

        foreach ($moves as [$amount, $from, $to]) {
            // One crate at a time, so the moved run arrives reversed.
            for ($i = 0; $i < $amount; $i++) {
                $crate = array_pop($stacks[$from - 1]);

                if ($crate !== null) {
                    $stacks[$to - 1][] = $crate;
                }
            }
        }

        return Result::of($this->tops($stacks), [
            'stacks' => count($stacks),
            'moves' => count($moves),
        ]);
    }

    public function partTwo(string $input): Result
    {
        [$stacks, $moves] = $this->parse($input);

        foreach ($moves as [$amount, $from, $to]) {
            // The whole run moves at once, keeping its order.
            $moved = array_splice($stacks[$from - 1], -$amount);

            foreach ($moved as $crate) {
                $stacks[$to - 1][] = $crate;
            }
        }

        return Result::of($this->tops($stacks), [
            'stacks' => count($stacks),
            'moves' => count($moves),
        ]);
    }

    /** @param list<list<string>> $stacks */
    private function tops(array $stacks): string
    {
        $tops = '';

        foreach ($stacks as $stack) {
            $tops .= $stack === [] ? '' : $stack[count($stack) - 1];
        }

        return $tops;
    }

    /**
     * Stacks are stored bottom-first, so array_pop takes the top crate.
     *
     * @return array{list<list<string>>, list<array{int, int, int}>}
     */
    private function parse(string $input): array
    {
        $stacks = [];
        $moves = [];
        $readingStacks = true;

        foreach (explode("\n", $input) as $line) {
            if (trim($line) === '') {
                $readingStacks = false;

                continue;
            }

            if ($readingStacks) {
                if (! str_contains($line, '[')) {
                    continue;
                }

                $count = (int) round(strlen($line) / 4);

                for ($i = 0; $i < $count; $i++) {
                    $stacks[$i] ??= [];
                    $crate = substr($line, ($i * 4) + 1, 1);

                    if (trim($crate) !== '') {
                        // Lines arrive top-down, so each lower crate goes under.
                        array_unshift($stacks[$i], $crate);
                    }
                }

                continue;
            }

            $parts = explode(' ', trim($line));

            if (count($parts) >= 6) {
                $moves[] = [(int) $parts[1], (int) $parts[3], (int) $parts[5]];
            }
        }

        ksort($stacks);

        return [array_values($stacks), $moves];
    }
}
