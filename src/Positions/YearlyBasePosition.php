<?php

namespace Proengeno\Invoice\Positions;

use DateTime;
use Proengeno\Invoice\Invoice;

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

    private static function calculateQuantity(DateTime $from, DateTime $until): float
    {
        $days = $until->format('L') ? 366 : 365;

        return round(Invoice::getCalulator()->multiply(
            Invoice::getCalulator()->divide(1, $days), $until->diff($from)->days + 1,
        ), 13);
    }
}
