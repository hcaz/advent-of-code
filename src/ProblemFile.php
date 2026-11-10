<?php

declare(strict_types=1);

namespace Aoc;

use RuntimeException;
use Symfony\Component\Yaml\Yaml;

/**
 * Read/write a single problem file in source/_problems/.
 *
 * These files are the only source of truth for a puzzle day, and two separate
 * tools write to them: bin/aoc records answers and notes, and the Gitea sync
 * records the issue number. Everything else in the file -- the title, solved_at,
 * the AI flags and all of the hand-written prose -- belongs to a human.
 *
 * So this class never rewrites a whole file. It mutates the specific keys it is
 * asked to and preserves the body byte-for-byte, because the cost of getting it
 * wrong is silently eating notes that were written by hand and not recoverable
 * from anywhere else.
 */
final class ProblemFile
{
    /**
     * Front matter key order. Keys are emitted in this order, with anything
     * unrecognised appended afterwards so a hand-added key is never dropped.
     */
    private const KEY_ORDER = [
        'extends',
        'year',
        'day',
        'title',
        'gitea_issue',
        'answer_one',
        'answer_two',
        'notes_one',
        'notes_two',
        'solved_at',
        'ai_part_one',
        'ai_part_two',
    ];

    /** @param array<string, mixed> $frontMatter */
    private function __construct(
        public readonly string $path,
        private array $frontMatter,
        private string $body,
    ) {}

    public static function read(string $path): self
    {
        $raw = @file_get_contents($path);

        if ($raw === false) {
            throw new RuntimeException("Cannot read problem file: {$path}");
        }

        [$yaml, $body] = self::split($raw, $path);

        $frontMatter = $yaml === '' ? [] : Yaml::parse($yaml);

        if (! is_array($frontMatter)) {
            throw new RuntimeException("Front matter in {$path} is not a mapping");
        }

        return new self($path, $frontMatter, $body);
    }

    /** @param array<string, mixed> $frontMatter */
    public static function create(string $path, array $frontMatter, string $body = ''): self
    {
        return new self($path, $frontMatter, $body);
    }

    /**
     * Split raw file contents into [yaml, body].
     *
     * Deliberately mirrors how Jigsaw's FrontMatterParser decides where front
     * matter ends -- the first delimiter line after the opening one -- so that
     * a body containing a "---" line is handled the same way by both.
     */
    private static function split(string $raw, string $path): array
    {
        // Strip a UTF-8 BOM so the opening delimiter still matches.
        $raw = preg_replace('/^\x{FEFF}/u', '', $raw) ?? $raw;

        if (! preg_match('/^---\r?\n/', $raw)) {
            // No front matter at all; the whole file is body.
            return ['', $raw];
        }

        $afterOpen = preg_replace('/^---\r?\n/', '', $raw, 1) ?? '';

        // Closing delimiter: a line that is exactly "---".
        if (! preg_match('/^---[ \t]*(\r?\n|$)/m', $afterOpen, $m, PREG_OFFSET_CAPTURE)) {
            throw new RuntimeException("Unterminated front matter in {$path}");
        }

        $offset = $m[0][1];
        $yaml = substr($afterOpen, 0, $offset);
        $body = substr($afterOpen, $offset + strlen($m[0][0]));

        return [$yaml, $body];
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->frontMatter[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->frontMatter);
    }

    /** @return array<string, mixed> */
    public function frontMatter(): array
    {
        return $this->frontMatter;
    }

    public function body(): string
    {
        return $this->body;
    }

    /**
     * Set a front matter key. A null value removes the key, so a cleared answer
     * does not linger as an empty string.
     */
    public function set(string $key, mixed $value): self
    {
        if ($value === null) {
            unset($this->frontMatter[$key]);
        } else {
            $this->frontMatter[$key] = $value;
        }

        return $this;
    }

    /** @param array<string, mixed> $values */
    public function setMany(array $values): self
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    public function render(): string
    {
        $ordered = [];

        foreach (self::KEY_ORDER as $key) {
            if (array_key_exists($key, $this->frontMatter)) {
                $ordered[$key] = $this->frontMatter[$key];
            }
        }

        // Anything not in KEY_ORDER is kept rather than silently dropped.
        foreach ($this->frontMatter as $key => $value) {
            if (! array_key_exists($key, $ordered)) {
                $ordered[$key] = $value;
            }
        }

        // Inline flow style for the notes maps keeps them to one readable line;
        // everything else is block style.
        $yaml = Yaml::dump($ordered, 2, 4, Yaml::DUMP_NULL_AS_TILDE);

        return "---\n".$yaml."---\n".$this->body;
    }

    /**
     * Write atomically. An interrupted write must not leave a truncated file,
     * because the body is hand-written content with no other copy.
     */
    public function write(): void
    {
        $dir = dirname($this->path);

        if (! is_dir($dir) && ! @mkdir($dir, 0o755, true) && ! is_dir($dir)) {
            throw new RuntimeException("Cannot create directory: {$dir}");
        }

        $temp = tempnam($dir, '.problem-');

        if ($temp === false) {
            throw new RuntimeException("Cannot create temp file in {$dir}");
        }

        if (@file_put_contents($temp, $this->render()) === false) {
            @unlink($temp);
            throw new RuntimeException("Cannot write temp file: {$temp}");
        }

        @chmod($temp, 0o644);

        if (! @rename($temp, $this->path)) {
            @unlink($temp);
            throw new RuntimeException("Cannot move {$temp} into place at {$this->path}");
        }
    }
}
