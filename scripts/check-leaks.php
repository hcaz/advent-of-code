#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Scan a built site for puzzle inputs or puzzle prose.
 *
 *   php scripts/check-leaks.php [build_production]
 *
 * This duplicates the afterBuild listener in bootstrap.php on purpose.
 *
 * The listener is convenient but fragile: Jigsaw promotes warnings to
 * exceptions while loading bootstrap.php, so a trivial mistake in that file
 * aborts it before the listener is ever registered -- and the build then
 * succeeds with no leak check at all. That happened during development, which
 * is why this standalone check exists and why CI runs it as its own step.
 *
 * Exit codes: 0 clean (or nothing to check), 1 leaked, 2 usage error.
 */

use Aoc\LeakScanner;

require __DIR__.'/../vendor/autoload.php';

$buildDir = $_SERVER['argv'][1] ?? 'build_production';

if (! is_dir($buildDir)) {
    fwrite(STDERR, "No such build directory: {$buildDir}\n");
    exit(2);
}

$scanner = new LeakScanner;

if ($scanner->isLocked()) {
    echo "check-leaks: data/ is encrypted, nothing could have been read. Skipping.\n";
    exit(0);
}

$fingerprints = $scanner->fingerprints();

if ($fingerprints === []) {
    // Not "clean" -- it means the scan had nothing to look for, which would
    // make a pass meaningless. Say so loudly rather than reporting success.
    fwrite(STDERR, 'check-leaks: no fingerprints could be derived from data/. '
        ."Refusing to report a pass, because the scan would prove nothing.\n");
    exit(2);
}

$leaks = $scanner->scan($buildDir);

if ($leaks === []) {
    printf(
        "check-leaks: %s is clean (%d fingerprints checked).\n",
        $buildDir,
        count($fingerprints),
    );
    exit(0);
}

fwrite(STDERR, "\ncheck-leaks: PUZZLE CONTENT FOUND IN {$buildDir}\n\n");

foreach (array_slice($leaks, 0, 20) as $leak) {
    fwrite(STDERR, "  {$leak['file']}\n    contains content from {$leak['source']}\n");
}

if (count($leaks) > 20) {
    fwrite(STDERR, '  ... and '.(count($leaks) - 20)." more\n");
}

fwrite(STDERR, "\n  This site is deployed publicly and the repo is mirrored to public\n"
    ."  GitHub and GitLab. Puzzle inputs and puzzle text must not be republished.\n");

exit(1);
