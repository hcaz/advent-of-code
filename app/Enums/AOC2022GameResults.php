<?php

namespace App\Enums;

enum AOC2022GameResults
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
