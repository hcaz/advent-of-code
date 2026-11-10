#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Regenerate README.md from readme_template.md plus the problems collection.
 *
 *   php scripts/sync-readme.php [--check]
 *
 * --check writes nothing and exits 1 if the README is out of date, which makes
 * it usable as a CI assertion.
 *
 * Reads only the problem files, never data/, so it works without the
 * git-crypt key.
 *
 * Two fixes over the old sync-readme command:
 *   - It used base_path(), which pointed at the app directory. After the app
 *     moved into a subfolder it was writing to a README that did not exist.
 *     Paths here are resolved from the repo root.
 *   - It walked years with a positional `$year = 2015; $year++` counter that
 *     assumed a dense, in-order list. This iterates the actual data.
 */

use Aoc\Calendar;
use Aoc\ProblemFile;

require __DIR__.'/../vendor/autoload.php';

$root = dirname(__DIR__);
$templatePath = $root.'/readme_template.md';
$readmePath = $root.'/README.md';
$placeholder = '<!-- CHALLENGE_DATA -->';

$check = in_array('--check', $_SERVER['argv'], true);

$template = @file_get_contents($templatePath);

if ($template === false) {
    fwrite(STDERR, "Cannot read {$templatePath}\n");
    exit(2);
}

if (! str_contains($template, $placeholder)) {
    fwrite(STDERR, "readme_template.md no longer contains {$placeholder}\n");
    exit(2);
}

// Group the released days by year, keeping day order.
$byYear = [];

foreach (Calendar::releasedDays() as [$year, $day]) {
    $path = $root.'/'.Calendar::path($year, $day);

    if (! is_file($path)) {
        continue;
    }

    $byYear[$year][$day] = ProblemFile::read($path);
}

krsort($byYear);

$block = '';
$totalStars = 0;

foreach ($byYear as $year => $days) {
    $lines = [];
    $yearStars = 0;

    foreach ($days as $day => $file) {
        $stars = 0;

        foreach (['answer_one', 'answer_two'] as $key) {
            if (trim((string) $file->get($key, '')) !== '') {
                $stars++;
            }
        }

        if ($stars === 0) {
            continue;
        }

        $yearStars += $stars;

        $title = (string) ($file->get('title') ?? 'Day '.$day);
        $marker = str_repeat('★', $stars);
        $retro = Calendar::isRetroactive($file->get('solved_at'), $year) ? ' _(retroactive)_' : '';

        $lines[] = sprintf(
            '- [%s](%s) %s - `bin/aoc run %d %d`%s',
            $title,
            Calendar::link($year, $day),
            $marker,
            $year,
            $day,
            $retro,
        );
    }

    if ($lines === []) {
        continue;
    }

    $totalStars += $yearStars;
    $block .= sprintf("\n### %d — ★ %d\n\n", $year, $yearStars);
    $block .= implode("\n", $lines)."\n";
}

$block = sprintf("\n**★ %d collected.**\n%s", $totalStars, $block);

$readme = str_replace($placeholder, $block, $template);

if ($check) {
    $current = is_file($readmePath) ? (string) file_get_contents($readmePath) : '';

    if ($current === $readme) {
        echo "sync-readme: README.md is up to date.\n";
        exit(0);
    }

    fwrite(STDERR, "sync-readme: README.md is out of date. Run: php scripts/sync-readme.php\n");
    exit(1);
}

if (@file_put_contents($readmePath, $readme) === false) {
    fwrite(STDERR, "Cannot write {$readmePath}\n");
    exit(2);
}

printf("sync-readme: wrote README.md (%d stars across %d years).\n", $totalStars, count($byYear));
