<?php

declare(strict_types=1);

namespace Aoc;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Looks for puzzle inputs or puzzle prose in a built site.
 *
 * The gate in src/Data.php is the primary defence; this is the independent
 * check that it actually worked. It exists because the failure is silent and
 * expensive: the site deploys publicly and the repo is mirrored to public
 * GitHub and GitLab, so nobody would notice until it was already published.
 *
 * Fingerprints are derived from data/ at runtime and never written anywhere.
 * A committed list of hashes of the plaintext would itself be a weak oracle in
 * a public repo, which is exactly what we are trying to avoid.
 */
final class LeakScanner
{
    /** Lines shorter than this are too common to be a reliable fingerprint. */
    private const MIN_LINE = 25;

    private const PRINT_LENGTH = 40;

    private readonly string $dataRoot;

    public function __construct(?string $dataRoot = null)
    {
        // See the note in Data: never default to a relative path, or a changed
        // working directory disables the scan without failing.
        $this->dataRoot = $dataRoot ?? dirname(__DIR__).'/data';
    }

    /**
     * Distinctive slices of every readable puzzle file.
     *
     * Several slices per file, so a partial render is caught as well as a
     * wholesale one.
     *
     * @return array<string, string> fingerprint => source path
     */
    public function fingerprints(): array
    {
        $prints = [];

        foreach (glob($this->dataRoot.'/*/*/*') ?: [] as $path) {
            $contents = (string) @file_get_contents($path);

            // Still encrypted, i.e. no git-crypt key: nothing to fingerprint.
            if ($contents === '' || str_starts_with($contents, "\0GITCRYPT\0")) {
                continue;
            }

            $lines = array_values(array_filter(
                array_map('trim', explode("\n", $contents)),
                fn (string $line) => strlen($line) >= self::MIN_LINE,
            ));

            if ($lines === []) {
                continue;
            }

            // Beginning, middle and end, so truncation does not hide a leak.
            $samples = [
                $lines[0],
                $lines[intdiv(count($lines), 2)],
                $lines[count($lines) - 1],
            ];

            foreach ($samples as $sample) {
                $prints[substr($sample, 0, self::PRINT_LENGTH)] = $path;
            }
        }

        return $prints;
    }

    /**
     * Scan a built site.
     *
     * @return list<array{file: string, source: string}> empty when clean
     */
    public function scan(string $buildDir): array
    {
        $prints = $this->fingerprints();

        if ($prints === []) {
            return [];
        }

        if (! is_dir($buildDir)) {
            return [];
        }

        $leaks = [];

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($buildDir, RecursiveDirectoryIterator::SKIP_DOTS),
        );

        foreach ($files as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $contents = (string) @file_get_contents($file->getPathname());

            foreach ($prints as $print => $source) {
                if (str_contains($contents, $print)) {
                    $leaks[] = [
                        'file' => $file->getPathname(),
                        'source' => $source,
                    ];

                    // One hit is enough to fail the file.
                    break;
                }
            }
        }

        return $leaks;
    }

    /** True when there is no git-crypt key, so there is nothing to check. */
    public function isLocked(): bool
    {
        return (new Data($this->dataRoot))->isLocked();
    }
}
