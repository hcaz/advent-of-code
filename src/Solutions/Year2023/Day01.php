<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2023;

use Aoc\PartNotImplemented;
use Aoc\Result;
use Aoc\Solution;

/**
 * 2023 day 1: Trebuchet?!
 *
 * Part one only -- the recorded answers agree (one star).
 *
 * The original did have a part-two branch, but it only re-found numeric digits
 * by a different route, which is the same problem part one already solves. The
 * actual part two requires recognising spelled-out digits ("one", "two", ...),
 * which it never did, and no answer was ever recorded. Porting it would have
 * displayed a confident-looking wrong number, so it reports as unsolved.
 */
final class Day01 implements Solution
{
    public function partOne(string $input): Result
    {
        $lines = array_values(array_filter(
            array_map('trim', explode("\n", $input)),
            fn (string $line) => $line !== '',
        ));

        $sum = 0;

        foreach ($lines as $line) {
            $digits = preg_replace('/[^0-9]/', '', $line) ?? '';

            if ($digits === '') {
                continue;
            }

            $sum += (int) (substr($digits, 0, 1).substr($digits, -1));
        }

        return Result::of($sum, ['lines' => count($lines)]);
    }

    public function partTwo(string $input): Result
    {
        throw PartNotImplemented::for(2023, 1, 'two');
    }
}
