<?php

declare(strict_types=1);

namespace Aoc;

use RuntimeException;
use Throwable;

/** Locates and executes solutions. */
final class Runner
{
    public function __construct(
        private readonly Data $data = new Data,
    ) {}

    /** Fully-qualified class name for a day, whether or not it exists. */
    public static function classFor(int $year, int $day): string
    {
        return sprintf('Aoc\\Solutions\\Year%d\\Day%02d', $year, $day);
    }

    public static function exists(int $year, int $day): bool
    {
        return class_exists(self::classFor($year, $day));
    }

    /** Every ported (year, day), in order. @return list<array{int, int}> */
    public static function all(): array
    {
        $found = [];

        foreach (glob('src/Solutions/Year*/Day*.php') ?: [] as $path) {
            if (preg_match('#Year(\d{4})/Day(\d{2})\.php$#', $path, $m)) {
                $found[] = [(int) $m[1], (int) $m[2]];
            }
        }

        usort($found, fn (array $a, array $b) => [$a[0], $a[1]] <=> [$b[0], $b[1]]);

        return $found;
    }

    public function solution(int $year, int $day): Solution
    {
        $class = self::classFor($year, $day);

        if (! class_exists($class)) {
            throw new RuntimeException("No solution for {$year} day {$day} ({$class})");
        }

        $solution = new $class;

        if (! $solution instanceof Solution) {
            throw new RuntimeException("{$class} does not implement ".Solution::class);
        }

        return $solution;
    }

    /**
     * Run one part.
     *
     * Returns null when the part is not implemented or the input is missing, so
     * a partial day does not abort a whole run.
     */
    public function run(int $year, int $day, string $part): ?Result
    {
        $input = $this->data->input($year, $day);

        if ($input === null) {
            return null;
        }

        $solution = $this->solution($year, $day);
        $method = $part === 'one' ? 'partOne' : 'partTwo';

        try {
            return $solution->$method($input);
        } catch (Throwable $e) {
            throw new RuntimeException(
                "{$year} day {$day} part {$part} threw: ".$e->getMessage(),
                previous: $e,
            );
        }
    }
}
