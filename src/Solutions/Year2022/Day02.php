<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2022;

use Aoc\Enums\GameObject;
use Aoc\Enums\GameResult;
use Aoc\Result;
use Aoc\Solution;

/**
 * 2022 day 2: Rock Paper Scissors.
 *
 * The reason the interface has no shared parse(): X/Y/Z are shapes in part one
 * and desired outcomes in part two, so the same three characters mean different
 * things depending on which part is running.
 *
 * Note the original printed its answer through number_format(), which would
 * have made this port disagree with the recorded answer of 13268/15508.
 */
final class Day02 implements Solution
{
    private const OPPONENT = [
        'A' => GameObject::ROCK,
        'B' => GameObject::PAPER,
        'C' => GameObject::SCISSORS,
    ];

    public function partOne(string $input): Result
    {
        $score = 0;
        $won = 0;
        $lost = 0;
        $drawn = 0;

        $shapes = ['X' => GameObject::ROCK, 'Y' => GameObject::PAPER, 'Z' => GameObject::SCISSORS];

        foreach ($this->rounds($input) as [$opponentKey, $youKey]) {
            $opponent = self::OPPONENT[$opponentKey];
            $you = $shapes[$youKey];

            $score += $you->score();

            if ($you->beats() === $opponent) {
                $score += GameResult::WIN->score();
                $won++;
            } elseif ($you->losesTo() === $opponent) {
                $score += GameResult::LOSS->score();
                $lost++;
            } else {
                $score += GameResult::DRAW->score();
                $drawn++;
            }
        }

        return Result::of($score, [
            'rounds' => $won + $lost + $drawn,
            'won' => $won,
            'lost' => $lost,
            'drawn' => $drawn,
        ]);
    }

    public function partTwo(string $input): Result
    {
        $score = 0;
        $won = 0;
        $lost = 0;
        $drawn = 0;

        $outcomes = ['X' => GameResult::LOSS, 'Y' => GameResult::DRAW, 'Z' => GameResult::WIN];

        foreach ($this->rounds($input) as [$opponentKey, $youKey]) {
            $opponent = self::OPPONENT[$opponentKey];
            $outcome = $outcomes[$youKey];

            $score += $outcome->score();

            match ($outcome) {
                GameResult::WIN => [$score += $opponent->losesTo()->score(), $won++],
                GameResult::LOSS => [$score += $opponent->beats()->score(), $lost++],
                GameResult::DRAW => [$score += $opponent->score(), $drawn++],
            };
        }

        return Result::of($score, [
            'rounds' => $won + $lost + $drawn,
            'won' => $won,
            'lost' => $lost,
            'drawn' => $drawn,
        ]);
    }

    /** @return list<array{string, string}> */
    private function rounds(string $input): array
    {
        $rounds = [];

        foreach (explode("\n", $input) as $line) {
            $parts = explode(' ', trim($line));

            if (count($parts) === 2) {
                $rounds[] = [$parts[0], $parts[1]];
            }
        }

        return $rounds;
    }
}
