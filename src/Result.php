<?php

declare(strict_types=1);

namespace Aoc;

use InvalidArgumentException;

/**
 * The answer to one part of one puzzle, plus notes from working it out.
 *
 * Two constraints are enforced here rather than left to convention:
 *
 * 1. The answer is always a string. The old data stored 2015/01's answers as
 *    JSON ints and everything else as strings, and comparing across that drift
 *    is a recurring bug. One type, decided here.
 *
 * 2. Notes are a flat map of label => scalar. Notes are committed to a repo
 *    that is mirrored to public GitHub and GitLab, so the shape is deliberately
 *    too narrow to hold a dump of the puzzle input. The old TUI printed whole
 *    parsed grids -- AOC2022Five::displayStacks() rendered the actual crate
 *    stacks -- and porting that verbatim would have published input-derived
 *    content. A careless port now fails loudly instead.
 */
final readonly class Result
{
    /** @var array<string, string|int|float|bool> */
    public array $notes;

    /** @param array<array-key, mixed> $notes */
    public function __construct(
        public string $answer,
        array $notes = [],
    ) {
        $this->notes = self::validateNotes($notes);
    }

    /** @param array<array-key, mixed> $notes */
    private static function validateNotes(array $notes): array
    {
        $validated = [];

        foreach ($notes as $label => $value) {
            if (! is_string($label) || trim($label) === '') {
                throw new InvalidArgumentException(
                    'Note labels must be non-empty strings; got '.var_export($label, true),
                );
            }

            if (is_array($value) || is_object($value) || $value === null) {
                throw new InvalidArgumentException(
                    "Note '{$label}' must be a scalar. Notes are published, so they "
                    .'cannot carry structures derived from the puzzle input; '
                    .'summarise it as a number or a short string instead.',
                );
            }

            if (is_string($value) && (str_contains($value, "\n") || strlen($value) > 120)) {
                throw new InvalidArgumentException(
                    "Note '{$label}' is multi-line or over 120 characters. Notes are "
                    .'published, so they must be short summaries, never excerpts of '
                    .'the puzzle input or text.',
                );
            }

            $validated[$label] = $value;
        }

        return $validated;
    }

    /** Convenience for solutions that compute an integer answer. */
    public static function of(string|int|float $answer, array $notes = []): self
    {
        return new self((string) $answer, $notes);
    }
}
