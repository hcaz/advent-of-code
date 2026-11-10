<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2022;

use Aoc\PartNotImplemented;
use Aoc\Result;
use Aoc\Solution;

/**
 * 2022 day 7: No Space Left On Device.
 *
 * Part one only -- part two was never finished, and the recorded answers agree
 * (one star). The original's partTwo was abandoned mid-debug: it still had
 * dump() calls, a dd() that would have halted execution, and a call to a
 * calculateSize() method that does not exist anywhere in the class. Rather
 * than guess at what it was meant to be, this reports honestly that the part
 * is unsolved.
 *
 * Part one keeps the original's approach: flatten the filesystem into
 * dot-delimited paths, nest it with Collection::undot(), then walk the tree
 * summing sizes and accumulating every directory at or under the limit. Note
 * that dots in filenames are replaced with underscores first, because undot()
 * would otherwise treat them as path separators.
 */
final class Day07 implements Solution
{
    private const LIMIT = 100000;

    public function partOne(string $input): Result
    {
        $tree = $this->tree($input);

        $total = 0;
        $directories = 0;

        $this->walk($tree, $total, $directories);

        return Result::of($total, [
            'directories' => $directories,
            'limit' => self::LIMIT,
        ]);
    }

    public function partTwo(string $input): Result
    {
        throw PartNotImplemented::for(2022, 7, 'two');
    }

    /**
     * Sum a directory subtree, accumulating any directory within the limit.
     *
     * @param  array<string, mixed>  $node
     */
    private function walk(array $node, int &$total, int &$directories): int
    {
        $size = 0;

        foreach ($node as $child) {
            $size += is_array($child)
                ? $this->walk($child, $total, $directories)
                : (int) $child;
        }

        $directories++;

        if ($size <= self::LIMIT) {
            $total += $size;
        }

        return $size;
    }

    /** @return array<string, mixed> */
    private function tree(string $input): array
    {
        $flat = [];
        $prefix = '';

        foreach (explode("\n", $input) as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            if (str_contains($line, '$')) {
                $command = explode(' ', str_replace('$ ', '', $line));

                if ($command[0] !== 'cd') {
                    continue;
                }

                $target = ltrim($command[1] ?? '', '/');

                if ($target === '..') {
                    $cut = strrpos($prefix, '.');
                    $prefix = $cut === false ? '' : substr($prefix, 0, $cut);

                    continue;
                }

                $flat[$prefix.$target] = [];
                $prefix = ltrim($prefix.$target.'.', '.');

                continue;
            }

            $entry = explode(' ', $line);

            if ($entry[0] !== 'dir' && count($entry) === 2) {
                // undot() treats dots as separators, so a filename's dot has
                // to become something else first.
                $name = str_replace('.', '_', $entry[1]);
                $flat[$prefix.$name] = $entry[0];
            }
        }

        return collect($flat)->undot()->toArray();
    }
}
