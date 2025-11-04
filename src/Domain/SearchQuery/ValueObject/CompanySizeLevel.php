<?php

declare(strict_types=1);

namespace App\Domain\SearchQuery\ValueObject;

enum CompanySizeLevel: string
{
    case LEVEL_1 = '1'; // 1–50 employés
    case LEVEL_2 = '2'; // 51–100 employés
    case LEVEL_3 = '3'; // 101–200 employés
    case LEVEL_4 = '4'; // 201–500 employés
    case LEVEL_5 = '5'; // 501+ employés

    public function label(): string
    {
        return match ($this) {
            self::LEVEL_1 => '1–50 employés',
            self::LEVEL_2 => '51–100 employés',
            self::LEVEL_3 => '101–200 employés',
            self::LEVEL_4 => '201–500 employés',
            self::LEVEL_5 => '501+ employés',
        };
    }
}
