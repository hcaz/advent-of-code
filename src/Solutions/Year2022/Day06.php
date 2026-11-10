<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2022;

use Aoc\Result;
use Aoc\Solution;

/**
 * 2022 day 6: Tuning Trouble.
 *
 * The original scanned the entire buffer and kept the first match rather than
 * stopping at it. This returns as soon as the marker is found, which is the
 * same answer for a fraction of the work.
 */
final class Day06 implements Solution
{
    public function partOne(string $input): Result
    {
        return $this->findMarker($input, 4);
    }

    public function partTwo(string $input): Result
    {
        return $this->findMarker($input, 14);
    }

    private function findMarker(string $input, int $size): Result
    {
        $window = [];
        $length = strlen($input);

        for ($i = 0; $i < $length; $i++) {
            $char = $input[$i];

            // Whitespace is skipped but still counts towards the index, as in
            // the original.
            if (trim($char) === '') {
                continue;
            }

            $window[] = $char;

            if (count($window) > $size) {
                array_shift($window);
            }

            if (count($window) === $size && count(array_unique($window)) === $size) {
                return Result::of($i + 1, [
                    'marker length' => $size,
                    'buffer length' => $length,
                ]);
            }
        }

        return Result::of(0, ['marker length' => $size, 'found' => false]);
    }
}
