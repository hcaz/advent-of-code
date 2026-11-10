<?php

declare(strict_types=1);

namespace Aoc;

use RuntimeException;

/**
 * Thrown by a part that has not been solved yet.
 *
 * Distinct from a genuine failure so the runner can say "not implemented"
 * instead of reporting an error. Some days really are half-finished -- 2022
 * day 7 part two was abandoned mid-way, with dump() and dd() calls still in it
 * and a call to a method that never existed -- and the site should show that
 * honestly rather than pretend the part works.
 */
final class PartNotImplemented extends RuntimeException
{
    public static function for(int $year, int $day, string $part): self
    {
        return new self("{$year} day {$day} part {$part} is not implemented");
    }
}
