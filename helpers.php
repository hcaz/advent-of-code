<?php

declare(strict_types=1);

/**
 * Blade-callable helpers. Jigsaw auto-requires this file alongside config.php
 * (see TightenCo\Jigsaw\Container::loadConfiguration), so these are available
 * in every template without any registration.
 */

use Aoc\Calendar;
use Aoc\Data;

if (! function_exists('aoc_link')) {
    /** Canonical puzzle URL, derived so it cannot drift from the day. */
    function aoc_link(int $year, int $day): string
    {
        return Calendar::link($year, $day);
    }
}

if (! function_exists('aoc_stars')) {
    /**
     * Stars earned, 0-2.
     *
     * Derived from recorded answers only. File presence is not a signal:
     * 2024/10 has both answers but no step_two.md, while 2024/07 and 2024/08
     * have full puzzle text and no answers at all.
     */
    function aoc_stars(mixed $page): int
    {
        $stars = 0;

        foreach (['answer_one', 'answer_two'] as $key) {
            $value = is_array($page) ? ($page[$key] ?? null) : ($page->$key ?? null);

            if (is_scalar($value) && trim((string) $value) !== '') {
                $stars++;
            }
        }

        return $stars;
    }
}

if (! function_exists('aoc_is_attempted')) {
    /**
     * Has this day been started at all?
     *
     * Every released day has a problem file, so file presence proves nothing.
     * The real signals are a recorded answer, or an input sitting in data/.
     * Data::hasInput() is not gated -- it reveals that a puzzle was attempted,
     * never its contents -- so this works in production too.
     */
    function aoc_is_attempted(mixed $page): bool
    {
        if (aoc_stars($page) > 0) {
            return true;
        }

        $year = (int) (is_array($page) ? ($page['year'] ?? 0) : ($page->year ?? 0));
        $day = (int) (is_array($page) ? ($page['day'] ?? 0) : ($page->day ?? 0));

        return $year > 0 && (new Data)->hasInput($year, $day);
    }
}

if (! function_exists('aoc_title')) {
    /**
     * A day's display title.
     *
     * Deliberately avoids `$page->title ?? ...`: Jigsaw merges site config into
     * page variables, so an absent page title resolves to the site name rather
     * than null. Only a non-empty, non-site value counts.
     */
    function aoc_title(mixed $page): string
    {
        $title = is_array($page) ? ($page['title'] ?? null) : ($page->title ?? null);
        $siteName = is_array($page) ? ($page['site_name'] ?? null) : ($page->site_name ?? null);
        $day = is_array($page) ? ($page['day'] ?? null) : ($page->day ?? null);

        if (is_string($title) && trim($title) !== '' && $title !== $siteName) {
            return $title;
        }

        return 'Day '.$day;
    }
}

if (! function_exists('aoc_is_retroactive')) {
    /** Was this solved outside its own Dec 1-25 event window? */
    function aoc_is_retroactive(?string $solvedAt, int $year): bool
    {
        return Calendar::isRetroactive($solvedAt, $year);
    }
}

if (! function_exists('aoc_puzzle')) {
    /**
     * Puzzle text and input for a day.
     *
     * $showText MUST be passed from $page->show_puzzle_text. It defaults to
     * false so that a forgotten argument withholds the text rather than
     * publishing it to a repo that is mirrored publicly.
     *
     * @return array{
     *     step_one: ?string, step_two: ?string, input: ?string,
     *     has_step_one: bool, has_step_two: bool, has_input: bool,
     *     input_bytes: ?int, locked: bool
     * }
     */
    function aoc_puzzle(int $year, int $day, bool $showText = false): array
    {
        $data = $showText ? Data::unlocked() : new Data;

        return [
            'step_one' => $data->stepOne($year, $day),
            'step_two' => $data->stepTwo($year, $day),
            'input' => $data->input($year, $day),
            'has_step_one' => $data->hasStepOne($year, $day),
            'has_step_two' => $data->hasStepTwo($year, $day),
            'has_input' => $data->hasInput($year, $day),
            'input_bytes' => $data->inputBytes($year, $day),
            'locked' => $data->isLocked(),
        ];
    }
}

if (! function_exists('aoc_problems_for_year')) {
    /**
     * A year's 25 calendar cells, in day order.
     *
     * Days with no problem file are absent from the collection, so the slot is
     * null. Absence IS the "not attempted" state -- nothing needs storing for
     * a day that was never touched.
     *
     * @return array<int, mixed>
     */
    function aoc_problems_for_year(mixed $problems, int $year): array
    {
        $byDay = [];

        foreach ($problems as $problem) {
            if ((int) $problem->year === $year) {
                $byDay[(int) $problem->day] = $problem;
            }
        }

        $cells = [];

        for ($day = 1; $day <= Calendar::LAST_DAY; $day++) {
            $cells[$day] = $byDay[$day] ?? null;
        }

        return $cells;
    }
}
