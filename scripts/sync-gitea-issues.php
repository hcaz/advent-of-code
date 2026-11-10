#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Keep one Gitea issue per puzzle day in step with the problem files.
 *
 *   php scripts/sync-gitea-issues.php --dry-run    show what would change
 *   php scripts/sync-gitea-issues.php              apply it
 *
 * LOCAL ONLY. Issue bodies embed the puzzle text, so this needs the git-crypt
 * key and must never run in CI. The old workflow did try to run it in a
 * key-less container, which would have written encrypted binary into every
 * issue body; the guard below refuses rather than repeating that.
 *
 * Also creates problem files for newly released days, so running this in
 * December is all that is needed to pick up a new puzzle.
 *
 * Fixes carried over from the old command:
 *   - Milestones are looked up (and created) by title instead of a hardcoded
 *     2015-2025 map, which had no entry for 2026 and would have crashed.
 *   - The issue NUMBER is stored after a create. The old code stored the
 *     global id there, which is a different value, so some stored ids are
 *     wrong and get corrected on the next run.
 *   - The token goes in an Authorization header, not a query string.
 *   - The answers block uses a real emptiness check. The old one used isset(),
 *     so unsolved days got a block of empty backticks.
 *   - Config comes from config.local.php rather than a .env whose example file
 *     was missing ISSUES_GITEA_ORG entirely, so the org was empty in CI and
 *     every request went to /repos//<repo>/issues.
 */

use Aoc\Calendar;
use Aoc\Data;
use Aoc\ProblemFile;

require __DIR__.'/../vendor/autoload.php';

$root = dirname(__DIR__);
chdir($root);

$dryRun = in_array('--dry-run', $_SERVER['argv'], true);

/*
 * Credentials come from config.local.php, which is gitignored. There is no
 * .env: the Jigsaw local config already exists, already holds the other
 * local-only setting (show_puzzle_text), and keeping one file means one thing
 * to keep out of git -- which matters because this repo is mirrored publicly.
 */
$configPath = $root.'/config.local.php';

if (! is_file($configPath)) {
    fwrite(STDERR, "No config.local.php. Copy config.local.php.example and fill in the gitea block.\n");
    exit(2);
}

$config = require $configPath;
$gitea = is_array($config['gitea'] ?? null) ? $config['gitea'] : [];

$missing = array_values(array_filter(
    ['host', 'org', 'repo', 'user', 'token'],
    fn (string $key) => trim((string) ($gitea[$key] ?? '')) === '',
));

if ($missing !== []) {
    fwrite(STDERR, 'config.local.php is missing gitea settings: '.implode(', ', $missing)."\n");
    exit(2);
}

$host = rtrim((string) $gitea['host'], '/');
$token = (string) $gitea['token'];
$org = (string) $gitea['org'];
$repo = (string) $gitea['repo'];
$user = (string) $gitea['user'];

$api = "{$host}/api/v1/repos/{$org}/{$repo}";

/**
 * @param  array<string, mixed>|null  $payload
 * @return array{int, mixed}
 */
function request(string $method, string $url, string $token, ?array $payload = null): array
{
    $handle = curl_init($url);

    $headers = [
        'Accept: application/json',
        "Authorization: token {$token}",
    ];

    if ($payload !== null) {
        $headers[] = 'Content-Type: application/json';
        curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($payload, JSON_THROW_ON_ERROR));
    }

    curl_setopt_array($handle, [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 30,
    ]);

    $body = curl_exec($handle);
    $status = (int) curl_getinfo($handle, CURLINFO_HTTP_CODE);
    $error = curl_error($handle);
    // No curl_close(): deprecated as of PHP 8.5, and a no-op since 8.0.

    if ($body === false) {
        fwrite(STDERR, "Request to {$url} failed: {$error}\n");
        exit(2);
    }

    return [$status, json_decode((string) $body, true)];
}

// --- Guard: issue bodies contain puzzle text, so the key must be present ----

$data = Data::unlocked();

if ($data->isLocked()) {
    fwrite(STDERR, "data/ is encrypted. Run `git-crypt unlock` first -- without the key this\n"
        ."would write encrypted binary into every issue body.\n");
    exit(2);
}

// --- Make sure every released day has a problem file ------------------------

$created = 0;

foreach (Calendar::releasedDays() as [$year, $day]) {
    $path = Calendar::path($year, $day);

    if (is_file($path)) {
        continue;
    }

    printf("%s problem file %s\n", $dryRun ? 'would create' : 'creating', $path);

    if (! $dryRun) {
        ProblemFile::create($path, [
            'extends' => '_layouts.problem',
            'year' => $year,
            'day' => $day,
        ])->write();
    }

    $created++;
}

// Year pages too, so a new year appears in the calendar.
foreach (array_unique(array_map(fn (array $p) => $p[0], Calendar::releasedDays())) as $year) {
    $path = "source/_years/{$year}.md";

    if (is_file($path)) {
        continue;
    }

    printf("%s year page %s\n", $dryRun ? 'would create' : 'creating', $path);

    if (! $dryRun) {
        file_put_contents($path, "---\nextends: _layouts.year\nyear: {$year}\n---\n");
    }

    $created++;
}

// --- Milestones, by title rather than a hardcoded map ----------------------

$milestones = [];
$page = 1;

do {
    [$status, $batch] = request('GET', "{$api}/milestones?state=all&limit=50&page={$page}", $token);

    if ($status !== 200 || ! is_array($batch)) {
        fwrite(STDERR, "Could not list milestones (HTTP {$status}).\n");
        exit(2);
    }

    foreach ($batch as $milestone) {
        $milestones[(string) $milestone['title']] = (int) $milestone['id'];
    }

    $page++;
} while (count($batch) === 50);

function milestone_for(int $year, array &$milestones, string $api, string $token, bool $dryRun): ?int
{
    if (isset($milestones[(string) $year])) {
        return $milestones[(string) $year];
    }

    printf("%s milestone %d\n", $dryRun ? 'would create' : 'creating', $year);

    if ($dryRun) {
        return null;
    }

    [$status, $milestone] = request('POST', "{$api}/milestones", $token, ['title' => (string) $year]);

    if ($status >= 300 || ! is_array($milestone)) {
        fwrite(STDERR, "Could not create milestone {$year} (HTTP {$status}).\n");
        exit(2);
    }

    return $milestones[(string) $year] = (int) $milestone['id'];
}

// --- Existing issues, indexed by number and by title -----------------------

$byNumber = [];
$byTitle = [];
$page = 1;

do {
    [$status, $batch] = request(
        'GET',
        "{$api}/issues?state=all&type=issues&limit=50&page={$page}",
        $token,
    );

    if ($status !== 200 || ! is_array($batch)) {
        fwrite(STDERR, "Could not list issues (HTTP {$status}).\n");
        exit(2);
    }

    foreach ($batch as $issue) {
        $byNumber[(int) $issue['number']] = $issue;
        $byTitle[(string) $issue['title']] = $issue;
    }

    $page++;
} while (count($batch) === 50);

printf("\n%d issues and %d milestones on the remote.\n\n", count($byNumber), count($milestones));

// --- Reconcile each day ----------------------------------------------------

$stats = ['created' => 0, 'updated' => 0, 'unchanged' => 0, 'renumbered' => 0];

foreach (Calendar::releasedDays() as [$year, $day]) {
    $file = ProblemFile::read(Calendar::path($year, $day));

    $name = (string) ($file->get('title') ?? '');
    $title = $name === '' ? sprintf('Day %d', $day) : sprintf('Day %d: %s', $day, $name);

    $answers = [];

    foreach (['one' => 'Step 1', 'two' => 'Step 2'] as $part => $label) {
        $answer = trim((string) $file->get('answer_'.$part, ''));

        if ($answer !== '') {
            $answers[] = "- {$label}: `{$answer}`";
        }
    }

    $body = sprintf("# [%s](%s)\n", $title, Calendar::link($year, $day));

    foreach (['one' => ['Step 1', 'stepOne'], 'two' => ['Step 2', 'stepTwo']] as [$heading, $method]) {
        $text = $data->$method($year, $day);

        if ($text !== null && trim($text) !== '') {
            $body .= "\n## {$heading}:\n\n".rtrim($text)."\n";
        }
    }

    if ($answers !== []) {
        $body .= "\n## Answers\n\n".implode("\n", $answers)."\n";
    }

    $stars = count($answers);
    $state = $stars === 2 ? 'closed' : 'open';
    $labels = match ($stars) {
        2 => ['Status/★★'],
        1 => ['Status/★'],
        default => [],
    };

    // Find the existing issue: the stored number first, then the current title,
    // then the two legacy title formats the old command used to write.
    $stored = $file->get('gitea_issue');
    $issue = null;

    if (is_int($stored) && isset($byNumber[$stored])) {
        $issue = $byNumber[$stored];
    }

    if ($issue === null) {
        foreach ([
            $title,
            sprintf('Day %02d:', $day),
            sprintf('Day %02d - %d', $day, $year),
            sprintf('Day %d - Advent of Code %d', $day, $year),
            sprintf('Day %02d - Advent of Code %d', $day, $year),
        ] as $candidate) {
            if (isset($byTitle[$candidate])) {
                $issue = $byTitle[$candidate];
                break;
            }
        }
    }

    if ($issue === null) {
        $milestone = milestone_for($year, $milestones, $api, $token, $dryRun);

        printf("%s issue for %d day %-2d  %s\n", $dryRun ? 'would create' : 'creating', $year, $day, $title);

        if (! $dryRun) {
            $payload = [
                'title' => $title,
                'body' => $body,
                'assignees' => $stars > 0 ? [$user] : [],
                'due_date' => sprintf('%d-12-%02dT00:00:00Z', $year, $day),
            ];

            if ($milestone !== null) {
                $payload['milestone'] = $milestone;
            }

            [$status, $result] = request('POST', "{$api}/issues", $token, $payload);

            if ($status >= 300 || ! is_array($result)) {
                fwrite(STDERR, "Could not create issue for {$year}/{$day} (HTTP {$status}).\n");
                exit(2);
            }

            // The NUMBER, not the id -- the old command stored the id here.
            $file->set('gitea_issue', (int) $result['number'])->write();

            if ($labels !== []) {
                request('PUT', "{$api}/issues/{$result['number']}/labels", $token, ['labels' => $labels]);
            }

            if ($state === 'closed') {
                request('PATCH', "{$api}/issues/{$result['number']}", $token, ['state' => 'closed']);
            }
        }

        $stats['created']++;

        continue;
    }

    $number = (int) $issue['number'];

    // Correct a stored id that was never an issue number.
    if ($stored !== $number) {
        printf("  %d day %-2d  stored gitea_issue %s -> %d\n", $year, $day, var_export($stored, true), $number);

        if (! $dryRun) {
            $file->set('gitea_issue', $number)->write();
        }

        $stats['renumbered']++;
    }

    $currentLabels = array_map(
        fn (array $label) => (string) $label['name'],
        is_array($issue['labels'] ?? null) ? $issue['labels'] : [],
    );
    sort($currentLabels);
    $wanted = $labels;
    sort($wanted);

    $changes = [];

    if ((string) $issue['title'] !== $title) {
        $changes['title'] = $title;
    }

    if (trim((string) ($issue['body'] ?? '')) !== trim($body)) {
        $changes['body'] = $body;
    }

    if ((string) $issue['state'] !== $state) {
        $changes['state'] = $state;
    }

    if ($changes === [] && $currentLabels === $wanted) {
        $stats['unchanged']++;

        continue;
    }

    printf(
        "%s issue #%-4d %d day %-2d  %s\n",
        $dryRun ? 'would update' : 'updating',
        $number,
        $year,
        $day,
        implode(', ', array_merge(
            array_keys($changes),
            $currentLabels === $wanted ? [] : ['labels'],
        )),
    );

    if (! $dryRun) {
        if ($changes !== []) {
            $changes['assignees'] = $stars > 0 ? [$user] : [];
            request('PATCH', "{$api}/issues/{$number}", $token, $changes);
        }

        if ($currentLabels !== $wanted) {
            request('PUT', "{$api}/issues/{$number}/labels", $token, ['labels' => $labels]);
        }
    }

    $stats['updated']++;
}

printf(
    "\n%s: %d created, %d updated, %d unchanged, %d renumbered, %d files created.\n",
    $dryRun ? 'Dry run' : 'Done',
    $stats['created'],
    $stats['updated'],
    $stats['unchanged'],
    $stats['renumbered'],
    $created,
);

if ($dryRun) {
    echo "Nothing was written. Re-run without --dry-run to apply.\n";
}
