<?php

declare(strict_types=1);

namespace Aoc\Enums;

/** Round outcomes. Used by 2022 day 2. */
enum GameResult
{
    case WIN;
    case LOSS;
    case DRAW;

    public function score(): int
    {
        return match ($this) {
            self::WIN => 6,
            self::LOSS => 0,
            self::DRAW => 3,
        };
    }
}
