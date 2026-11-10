<?php

declare(strict_types=1);

namespace Aoc;

/**
 * One puzzle day.
 *
 * Each part takes the raw input and parses it itself. There is deliberately no
 * shared parse() step: 2022 day 2 reads the same three letters as shapes in
 * part one and as desired outcomes in part two, so a single parse would be
 * wrong for one of them.
 */
interface Solution
{
    public function partOne(string $input): Result;

    public function partTwo(string $input): Result;
}
