<?php

declare(strict_types=1);

namespace Aoc;

/**
 * The only reader of data/.
 *
 * Puzzle inputs and puzzle text are git-crypt encrypted because this repo is
 * mirrored to public GitHub and GitLab. They must never reach the built site
 * either, so access is gated here at the read layer rather than in templates:
 * a template that can see the text can leak it through a stray @dump, a meta
 * description or a feed, whereas a template handed null cannot.
 *
 * The gate defaults to CLOSED. Callers opt in explicitly, so a new build
 * environment or a forgotten argument withholds the text rather than
 * publishing it.
 */
final class Data
{
    /** git-crypt prefixes every encrypted blob with this. */
    private const CRYPT_MAGIC = "\0GITCRYPT\0";

    private readonly string $root;

    public function __construct(
        ?string $root = null,
        private readonly bool $allowPuzzleText = false,
    ) {
        // Resolved from the project root rather than defaulting to a relative
        // 'data', so a different working directory cannot make every lookup
        // silently return nothing.
        $this->root = $root ?? dirname(__DIR__).'/data';
    }

    /** A reader that may return puzzle text. For local builds and the CLI only. */
    public static function unlocked(?string $root = null): self
    {
        return new self($root, allowPuzzleText: true);
    }

    /**
     * The puzzle input.
     *
     * Gated: this is the single most sensitive file in the repo.
     */
    public function input(int $year, int $day): ?string
    {
        return $this->readGated($this->path($year, $day, 'input.txt'));
    }

    public function stepOne(int $year, int $day): ?string
    {
        return $this->readGated($this->path($year, $day, 'step_one.md'));
    }

    public function stepTwo(int $year, int $day): ?string
    {
        return $this->readGated($this->path($year, $day, 'step_two.md'));
    }

    /**
     * Whether the day has an input on disk, regardless of the gate.
     *
     * Safe to expose: it reveals that a puzzle was attempted, not its content.
     */
    public function hasInput(int $year, int $day): bool
    {
        return is_file($this->path($year, $day, 'input.txt'));
    }

    public function hasStepOne(int $year, int $day): bool
    {
        return is_file($this->path($year, $day, 'step_one.md'));
    }

    public function hasStepTwo(int $year, int $day): bool
    {
        return is_file($this->path($year, $day, 'step_two.md'));
    }

    /**
     * True when data/ is still encrypted, i.e. there is no git-crypt key.
     *
     * CI is deliberately key-less, so builds there must degrade to "no text
     * available" rather than rendering binary into the page.
     */
    public function isLocked(): bool
    {
        foreach (glob($this->root.'/*/*/input.txt') ?: [] as $path) {
            return $this->isEncrypted((string) @file_get_contents($path));
        }

        return false;
    }

    /**
     * Size of the input in bytes, for the local-only preview. Returns null when
     * the gate is closed so production cannot infer anything from it.
     */
    public function inputBytes(int $year, int $day): ?int
    {
        if (! $this->allowPuzzleText) {
            return null;
        }

        $path = $this->path($year, $day, 'input.txt');
        $size = is_file($path) ? filesize($path) : false;

        return $size === false ? null : $size;
    }

    private function path(int $year, int $day, string $file): string
    {
        return sprintf('%s/%d/%02d/%s', $this->root, $year, $day, $file);
    }

    private function readGated(string $path): ?string
    {
        if (! $this->allowPuzzleText || ! is_file($path)) {
            return null;
        }

        $contents = @file_get_contents($path);

        if ($contents === false || $this->isEncrypted($contents)) {
            return null;
        }

        return $contents;
    }

    private function isEncrypted(string $contents): bool
    {
        return str_starts_with($contents, self::CRYPT_MAGIC);
    }
}
