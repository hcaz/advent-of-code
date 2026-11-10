<?php

use Aoc\LeakScanner;
use Illuminate\Container\Container;
use TightenCo\Jigsaw\Events\EventBus;
use TightenCo\Jigsaw\Jigsaw;

/** @var Container $container */
/** @var EventBus $events */

/*
 * Fail the build if any puzzle input or puzzle prose made it into the output.
 *
 * src/Data.php is the primary gate; this is the independent check that it
 * worked. It runs in EVERY environment, not just production: build_local is
 * meant to contain puzzle text, but a mistake there would still be caught by
 * the destination check below, and running it everywhere means the scanner
 * itself is exercised constantly rather than only on release.
 *
 * Note Jigsaw::build() has no finally, so throwing here leaves any generated
 * collection files behind in the per-collection _tmp directories under source.
 * Those paths are gitignored.
 */
$events->afterBuild(function (Jigsaw $jigsaw) {
    $destination = $jigsaw->getDestinationPath();

    // build_local is allowed to contain puzzle text; that is its whole purpose.
    if (str_ends_with($destination, 'build_local')) {
        return;
    }

    $scanner = new LeakScanner;

    if ($scanner->isLocked()) {
        // No git-crypt key, so nothing can have been read, let alone leaked.
        return;
    }

    $leaks = $scanner->scan($destination);

    if ($leaks === []) {
        return;
    }

    $message = "\n  PUZZLE CONTENT LEAKED INTO THE BUILD\n\n";

    foreach (array_slice($leaks, 0, 10) as $leak) {
        $message .= "    {$leak['file']}\n      contains content from {$leak['source']}\n";
    }

    if (count($leaks) > 10) {
        $message .= '    ... and '.(count($leaks) - 10)." more\n";
    }

    // Delete the output as well as failing. The files were already written
    // before this listener ran, so leaving them would leave a deployable
    // directory full of puzzle text for anyone who ignored the error or ran a
    // deploy step by hand. The build directory is gitignored and regenerable,
    // so destroying it costs nothing.
    $removed = 0;

    if (is_dir($destination)) {
        $entries = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($destination, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($entries as $entry) {
            $entry->isDir() ? @rmdir($entry->getPathname()) : @unlink($entry->getPathname());
            $removed++;
        }

        @rmdir($destination);
    }

    $message .= "\n  Deleted the build output ({$removed} entries) so it cannot be deployed.\n";

    $message .= "\n  This site is deployed publicly and the repo is mirrored to public\n"
        ."  GitHub and GitLab. Puzzle inputs and puzzle text must not be republished.\n"
        ."  Check that puzzle content is only read through src/Data.php and only\n"
        ."  where show_puzzle_text is enabled.\n";

    throw new RuntimeException($message);
});
