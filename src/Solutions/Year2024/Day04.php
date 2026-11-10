<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2024;

use Aoc\Result;
use Aoc\Solution;

/**
 * 2024 day 4: Ceres Search.
 *
 * Step logic is the original, unchanged -- all eight directions spelled out for
 * part one and all four diagonal arrangements for part two, with the original's
 * own ASCII diagrams kept as comments.
 */
final class Day04 implements Solution
{
    /** @var list<list<string>> */
    private array $data = [];

    public function partOne(string $input): Result
    {
        $this->loadData($input);

        $xmasCount = 0;
        $rightBound = count($this->data[0]) - 3;
        $downBound = count($this->data) - 3;
        $leftBound = 2;
        $upBound = 2;

        foreach ($this->data as $lineIndex => $line) {
            foreach ($line as $charIndex => $char) {
                if ($char == 'X') {
                    // ....
                    // XMAS
                    // ....
                    // ....
                    if ($charIndex < $rightBound && $this->data[$lineIndex][$charIndex + 1] == 'M' && $this->data[$lineIndex][$charIndex + 2] == 'A' && $this->data[$lineIndex][$charIndex + 3] == 'S') {
                        $xmasCount++;
                    }
                    // ....
                    // SMAX
                    // ....
                    // ....
                    if ($charIndex > $leftBound && $this->data[$lineIndex][$charIndex - 1] == 'M' && $this->data[$lineIndex][$charIndex - 2] == 'A' && $this->data[$lineIndex][$charIndex - 3] == 'S') {
                        $xmasCount++;
                    }
                    // ..X.
                    // ..M.
                    // ..A.
                    // ..S.
                    if ($lineIndex < $downBound && $this->data[$lineIndex + 1][$charIndex] == 'M' && $this->data[$lineIndex + 2][$charIndex] == 'A' && $this->data[$lineIndex + 3][$charIndex] == 'S') {
                        $xmasCount++;
                    }
                    // ..S.
                    // ..A.
                    // ..M.
                    // ..X.
                    if ($lineIndex > $upBound && $this->data[$lineIndex - 1][$charIndex] == 'M' && $this->data[$lineIndex - 2][$charIndex] == 'A' && $this->data[$lineIndex - 3][$charIndex] == 'S') {
                        $xmasCount++;
                    }
                    // ...X
                    // ..M.
                    // .A..
                    // S...
                    if ($lineIndex < $downBound && $charIndex < $rightBound && $this->data[$lineIndex + 1][$charIndex + 1] == 'M' && $this->data[$lineIndex + 2][$charIndex + 2] == 'A' && $this->data[$lineIndex + 3][$charIndex + 3] == 'S') {
                        $xmasCount++;
                    }
                    // X...
                    // .M..
                    // ..A.
                    // ...S
                    if ($lineIndex < $downBound && $charIndex > $leftBound && $this->data[$lineIndex + 1][$charIndex - 1] == 'M' && $this->data[$lineIndex + 2][$charIndex - 2] == 'A' && $this->data[$lineIndex + 3][$charIndex - 3] == 'S') {
                        $xmasCount++;
                    }
                    // ...S
                    // ..A.
                    // .M..
                    // X...
                    if ($lineIndex > $upBound && $charIndex < $rightBound && $this->data[$lineIndex - 1][$charIndex + 1] == 'M' && $this->data[$lineIndex - 2][$charIndex + 2] == 'A' && $this->data[$lineIndex - 3][$charIndex + 3] == 'S') {
                        $xmasCount++;
                    }
                    // S...
                    // .A..
                    // ..M.
                    // ...X
                    if ($lineIndex > $upBound && $charIndex > $leftBound && $this->data[$lineIndex - 1][$charIndex - 1] == 'M' && $this->data[$lineIndex - 2][$charIndex - 2] == 'A' && $this->data[$lineIndex - 3][$charIndex - 3] == 'S') {
                        $xmasCount++;
                    }
                }
            }
        }

        return Result::of($xmasCount, ['grid' => count($this->data).'x'.count($this->data[0])]);
    }

    public function partTwo(string $input): Result
    {
        $this->loadData($input);

        $x_masCount = 0;
        $rightBound = count($this->data[0]) - 1;
        $downBound = count($this->data) - 1;
        $leftBound = 0;
        $upBound = 0;

        foreach ($this->data as $lineIndex => $line) {
            foreach ($line as $charIndex => $char) {
                if ($charIndex < $rightBound && $charIndex > $leftBound && $lineIndex < $downBound && $lineIndex > $upBound && $char == 'A') {
                    // M.S
                    // .A.
                    // M.S
                    if ($this->data[$lineIndex - 1][$charIndex - 1] == 'M' &&
                        $this->data[$lineIndex + 1][$charIndex + 1] == 'S' &&
                        $this->data[$lineIndex + 1][$charIndex - 1] == 'M' &&
                        $this->data[$lineIndex - 1][$charIndex + 1] == 'S') {
                        $x_masCount++;
                    }
                    // S.M
                    // .A.
                    // S.M
                    if ($this->data[$lineIndex + 1][$charIndex + 1] == 'M' &&
                        $this->data[$lineIndex - 1][$charIndex - 1] == 'S' &&
                        $this->data[$lineIndex - 1][$charIndex + 1] == 'M' &&
                        $this->data[$lineIndex + 1][$charIndex - 1] == 'S') {
                        $x_masCount++;
                    }
                    // M.M
                    // .A.
                    // S.S
                    if ($this->data[$lineIndex - 1][$charIndex - 1] == 'M' &&
                        $this->data[$lineIndex + 1][$charIndex + 1] == 'S' &&
                        $this->data[$lineIndex - 1][$charIndex + 1] == 'M' &&
                        $this->data[$lineIndex + 1][$charIndex - 1] == 'S') {
                        $x_masCount++;
                    }
                    // S.S
                    // .A.
                    // M.M
                    if ($this->data[$lineIndex + 1][$charIndex + 1] == 'M' &&
                        $this->data[$lineIndex - 1][$charIndex - 1] == 'S' &&
                        $this->data[$lineIndex + 1][$charIndex - 1] == 'M' &&
                        $this->data[$lineIndex - 1][$charIndex + 1] == 'S') {
                        $x_masCount++;
                    }
                }
            }
        }

        return Result::of($x_masCount, ['grid' => count($this->data).'x'.count($this->data[0])]);
    }

    private function loadData(string $input): void
    {
        $this->data = [];
        $input = explode("\n", $input);
        foreach ($input as $lineData) {
            if (empty($lineData)) {
                continue;
            }
            $line = [];
            for ($i = 0; $i < strlen($lineData); $i++) {
                $line[] = $lineData[$i];
            }
            $this->data[] = $line;
        }
    }
}
