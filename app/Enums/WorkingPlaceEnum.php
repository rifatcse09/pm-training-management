<?php

namespace App\Enums;

enum WorkingPlaceEnum: int
{
    case PLANNING_DIVISION = 1;
    case OPERATIONS_DIVISION = 2;
    case AGRICULTURE_DIVISION = 3;
    case INFRASTRUCTURE_DIVISION = 4;
    case INDUSTRY_DIVISION = 5;
    case SOCIOECONOMIC_DIVISION = 6;
    case GENERAL_ECONOMY_DIVISION = 7;

    public static function getNames(): array
    {
        return [
            self::PLANNING_DIVISION->value => 'পরিকল্পনা বিভাগ',
            self::OPERATIONS_DIVISION->value => 'কার্যক্রম বিভাগ',
            self::AGRICULTURE_DIVISION->value => 'কৃষি,পানি সম্পদ ও পল্লী প্রতিষ্ঠান বিভাগ',
            self::INFRASTRUCTURE_DIVISION->value => 'ভৌত অবকাঠামো বিভাগ',
            self::INDUSTRY_DIVISION->value => 'শিল্প ও শক্তি বিভাগ',
            self::SOCIOECONOMIC_DIVISION->value => 'আর্থ-সামাজিক অবকাঠামো বিভাগ',
            self::GENERAL_ECONOMY_DIVISION->value => 'সাধারণ অর্থনীতি বিভাগ',
        ];
    }

    public static function getNameById(?int $id): ?string
    {
        return self::getNames()[$id] ?? null;
    }
}
