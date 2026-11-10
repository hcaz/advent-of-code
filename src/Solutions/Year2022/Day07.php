<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2022;

use Aoc\PartNotImplemented;
use Aoc\Result;
use Aoc\Solution;
use Illuminate\Support\Collection;

/**
 * 2022 day 7: No Space Left On Device.
 *
 * Part one's step logic is the original, unchanged -- flatten the filesystem
 * into dot-delimited paths, nest with undot(), then recurse summing sizes and
 * accumulating any directory within the limit.
 *
 * Part two was never finished: the original still had dump() calls, a dd() that
 * would have halted execution, and a call to a calculateSize() method that does
 * not exist anywhere in the class. It never produced an answer and none was
 * recorded, so it reports as unimplemented rather than being guessed at.
 */
final class Day07 implements Solution
{
    private Collection $commands;

    private Collection $directories;

    private int $totalSize = 0;

    public function partOne(string $input): Result
    {
        $this->loadData($input);

        $this->parseDirectories();
        $this->calculateSizeOfFoldersUnderLimit();

        return Result::of($this->totalSize, ['commands' => $this->commands->count()]);
    }

    public function partTwo(string $input): Result
    {
        throw PartNotImplemented::for(2022, 7, 'two');
    }

    private function parseDirectories(): void
    {
        $directories = collect([]);
        $currentDirectoryPrefix = '';
        foreach ($this->commands as $command) {
            if (str_contains($command, '$')) {
                $command = explode(' ', str_replace('$ ', '', $command));
                if ($command[0] == 'cd') {
                    $command[1] = ltrim($command[1], '/');

                    if ($command[1] == '..') {
                        $currentDirectoryPrefix = substr($currentDirectoryPrefix, 0, strrpos($currentDirectoryPrefix, '.'));

                        continue;
                    }

                    $directories[$currentDirectoryPrefix.$command[1]] = [];

                    $currentDirectoryPrefix = ltrim($currentDirectoryPrefix.$command[1].'.', '.');
                }
            } else {
                $command = explode(' ', $command);
                if ($command[0] != 'dir' && count($command) == 2) {
                    // /Replace file extension dot with a underscore so I can use undot later
                    $command[1] = str_replace('.', '_', $command[1]);
                    $directories[$currentDirectoryPrefix.$command[1]] = $command[0];
                }
            }
        }

        $this->directories = $directories->undot();
    }

    private function calculateSizeOfFoldersUnderLimit($children = null, $limit = 100000): int
    {
        $size = 0;

        if (is_null($children)) {
            $children = $this->directories->toArray();
        }

        if (is_numeric($children)) {
            $size += $children;
        } elseif (is_array($children)) {
            foreach ($children as $childChildren) {
                $size += $this->calculateSizeOfFoldersUnderLimit($childChildren, $limit);
            }
            if ($size <= $limit) {
                $this->totalSize += $size;
            }
        }

        return $size;
    }

    private function loadData(string $input): void
    {
        $this->totalSize = 0;
        $this->commands = collect(explode("\n", $input));
    }
}
