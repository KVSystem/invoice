<?php

namespace Proengeno\Invoice\Positions;

use DateTime;

class YearlyBasePosition extends PeriodPosition
{
    public function __construct(string $name, float $price, DateTime $from, DateTime $until)
    {
        parent::__construct($name, $price, self::calculateQuantity($from, $until), $from, $until);
    }

    public static function fromArray(array $attributes)
    {
        extract($attributes);

        return new static($name, $price, new DateTime($from), new DateTime($until));
    }

    private static function calculateQuantity(DateTime $from, DateTime $until)
    {
        $days = $until->format('L') ? 366 : 365;
        return round(bcmul(bcdiv(1, $days, 16), ($until->diff($from)->days + 1), 16), 13);
    }
}
