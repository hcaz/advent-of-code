<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2022;

use Aoc\Result;
use Aoc\Solution;

/**
 * 2022 day 6: Tuning Trouble.
 *
 * Step logic is the original, unchanged -- it scans the whole buffer and keeps
 * the first marker it found rather than stopping there.
 */
final class Day06 implements Solution
{
    private string $buffer;

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
        $this->loadData($input);

        $indexOfFirstMarker = null;

        $localBuffer = collect([]);
        for ($i = 0; $i < strlen($this->buffer); $i++) {
            $char = $this->buffer[$i];
            if (empty(trim($char))) {
                continue;
            }

            $localBuffer->push($char);
            if ($localBuffer->count() > $size) {
                $localBuffer->pull(0);
            }
            $localBuffer = $localBuffer->values();

            if ($localBuffer->count() == $size && $localBuffer->unique()->count() == $size) {
                if (is_null($indexOfFirstMarker)) {
                    $indexOfFirstMarker = ($i + 1);
                }
            }
        }

        return Result::of($indexOfFirstMarker ?? 0, [
            'marker length' => $size,
            'buffer length' => strlen($this->buffer),
        ]);
    }

    private function loadData(string $input): void
    {
        $this->buffer = $input;
    }
}
