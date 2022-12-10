<?php

namespace App\Enums;

enum AOC2022GameObjects
{
    case ROCK;
    case PAPER;
    case SCISSORS;

    public function beats(): self
    {
        return match ($this) {
            self::ROCK => self::SCISSORS,
            self::PAPER => self::ROCK,
            self::SCISSORS => self::PAPER,
        };
    }

    public function losesTo(): self
    {
        return match ($this) {
            self::ROCK => self::PAPER,
            self::PAPER => self::SCISSORS,
            self::SCISSORS => self::ROCK,
        };
    }

    public function score(): int
    {
        return match ($this) {
            self::ROCK => 1,
            self::PAPER => 2,
            self::SCISSORS => 3,
        };
    }
}
