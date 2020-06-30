<?php

namespace Proengeno\Invoice\Positions;

use DateTime;
use InvalidArgumentException;
use Proengeno\Invoice\Invoice;

class MonthlyBasePosition extends PeriodPosition
{
    public function __construct(string $name, float $price, DateTime $from, DateTime $until)
    {
        parent::__construct($name, $price, self::calculateQuantity($from, $until), $from, $until);
    }

    private static function calculateQuantity(DateTime $from, DateTime $until): float
    {
        if ($until > $from) {
            return round(
                Invoice::getCalulator()->multiply("0.032876712" /* 12/365 */, $until->diff($from)->days + 1), 6
            );
        }
        throw new InvalidArgumentException($until->format('Y-m-d H:i:s') . ' must be greaten than ' . $from->format('Y-m-d H:i:s'));
    }

    public function yearlyAmount(): int
    {
        return (int) round(Invoice::getCalulator()->multiply(
            Invoice::getCalulator()->divide($this->amount(), $this->quantity()), 12
        ), 0);
    }
}
