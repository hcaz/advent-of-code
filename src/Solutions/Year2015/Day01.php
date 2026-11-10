<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2015;

use Aoc\Result;
use Aoc\Solution;

/**
 * 2015 day 1: Not Quite Lisp.
 *
 * Step logic is the original, unchanged. Only the plumbing differs: loadData()
 * takes the input as an argument instead of reading config, and the value that
 * was passed to alert() is returned instead.
 */
final class Day01 implements Solution
{
    private string $instructions;

    public function partOne(string $input): Result
    {
        $this->loadData($input);

        $up = substr_count($this->instructions, '(');
        $down = substr_count($this->instructions, ')');

        return Result::of($up - $down, ['up' => $up, 'down' => $down]);
    }

    public function partTwo(string $input): Result
    {
        $this->loadData($input);

        $answer = 0;
        $floor = 0;
        $position = 0;
        $length = strlen($this->instructions);
        while ($floor >= 0 && $position < $length) {
            $floor += $this->instructions[$position] == '(' ? 1 : -1;
            $position++;

            if ($floor < 0) {
                $answer = $position;
            }
        }

        return Result::of($answer, ['instructions' => $length]);
    }

    private function loadData(string $input): void
    {
        $this->instructions = $input;
    }
}
