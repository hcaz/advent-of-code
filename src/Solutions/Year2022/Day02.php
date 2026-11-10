<?php

declare(strict_types=1);

namespace Aoc\Solutions\Year2022;

use Aoc\Enums\GameObject;
use Aoc\Enums\GameResult;
use Aoc\Result;
use Aoc\Solution;
use Illuminate\Support\Collection;

/**
 * 2022 day 2: Rock Paper Scissors.
 *
 * Step logic is the original, unchanged, including the per-part reinterpretation
 * of X/Y/Z -- shapes in part one, desired outcomes in part two -- which is why
 * each part sets up its own $dataKey before loading. Only the number_format()
 * applied just before printing is dropped.
 */
final class Day02 implements Solution
{
    /** @var array<string, GameObject|GameResult> */
    private array $dataKey = [
        'A' => GameObject::ROCK,
        'B' => GameObject::PAPER,
        'C' => GameObject::SCISSORS,
    ];

    private Collection $rounds;

    public function partOne(string $input): Result
    {
        $this->dataKey['X'] = GameObject::ROCK;
        $this->dataKey['Y'] = GameObject::PAPER;
        $this->dataKey['Z'] = GameObject::SCISSORS;
        $this->loadData($input);

        $score = 0;
        $gamesWon = 0;
        $gamesLost = 0;
        $gamesDrawn = 0;
        foreach ($this->rounds as $round) {
            $score = $score + $round['you']->score();
            if ($round['you']->beats() === $round['opponent']) {
                $score = $score + GameResult::WIN->score();
                $gamesWon++;
            } elseif ($round['you']->losesTo() === $round['opponent']) {
                $score = $score + GameResult::LOSS->score();
                $gamesLost++;
            } else {
                $score = $score + GameResult::DRAW->score();
                $gamesDrawn++;
            }
        }

        return Result::of($score, [
            'rounds' => $this->rounds->count(),
            'won' => $gamesWon,
            'lost' => $gamesLost,
            'drawn' => $gamesDrawn,
        ]);
    }

    public function partTwo(string $input): Result
    {
        $this->dataKey['X'] = GameResult::LOSS;
        $this->dataKey['Y'] = GameResult::DRAW;
        $this->dataKey['Z'] = GameResult::WIN;
        $this->loadData($input);

        $score = 0;
        $gamesWon = 0;
        $gamesLost = 0;
        $gamesDrawn = 0;
        foreach ($this->rounds as $round) {
            $score = $score + $round['you']->score();

            switch ($round['you']) {
                case GameResult::WIN:
                    $gamesWon++;
                    $score = $score + $round['opponent']->losesTo()->score();
                    break;
                case GameResult::LOSS:
                    $gamesLost++;
                    $score = $score + $round['opponent']->beats()->score();
                    break;
                case GameResult::DRAW:
                    $gamesDrawn++;
                    $score = $score + $round['opponent']->score();
                    break;
            }
        }

        return Result::of($score, [
            'rounds' => $this->rounds->count(),
            'won' => $gamesWon,
            'lost' => $gamesLost,
            'drawn' => $gamesDrawn,
        ]);
    }

    private function loadData(string $input): void
    {
        $this->rounds = collect([]);
        foreach (explode("\n", $input) as $line) {
            if ($line == '') {
                continue;
            }

            $round = explode(' ', $line);
            if (count($round) == 2) {
                $this->rounds->push(['opponent' => $this->dataKey[$round[0]], 'you' => $this->dataKey[$round[1]]]);
            }
        }
    }
}
