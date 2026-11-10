<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2024;

use Aoc\PartNotImplemented;
use Aoc\Result;
use Aoc\Solution;

/**
 * 2024 day 7: Bridge Repair.
 *
 * Unsolved. The original was abandoned part-way through debugging: it built
 * operator combinations and then tried to evaluate them with
 * eval('$((...));'), which is shell arithmetic syntax and not valid PHP, with
 * a dd() and a bare exit; left in the loop. It never produced an answer and
 * none was ever recorded, so both parts report as unimplemented rather than
 * returning something that looks like a result.
 *
 * The input and part one's puzzle text are present, so the site shows this day
 * as in progress.
 */
final class Day07 implements Solution
{
    public function partOne(string $input): Result
    {
        throw PartNotImplemented::for(2024, 7, 'one');
    }

    public function partTwo(string $input): Result
    {
        throw PartNotImplemented::for(2024, 7, 'two');
    }
}
