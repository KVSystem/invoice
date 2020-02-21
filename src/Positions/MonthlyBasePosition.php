<?php

namespace Proengeno\Invoice\Positions;

use DateTime;

class MonthlyBasePosition extends PeriodPosition
{
    public function __construct(string $name, DateTime $from, DateTime $until, float $price)
    {
        parent::__construct($from, $until, new Position($name, $price, self::calculateQuantity($from, $until)));
    }

    private static function calculateQuantity(DateTime $from, DateTime $until)
    {
        return round(bcmul(bcdiv(12, 365, 16), ($until->diff($from)->days + 1), 16), 13);
    }

    public function yearlyAmount(): int
    {
        return (int)round(bcmul(bcdiv($this->amount(), $this->quantity(), 3) * 12, 1), 0);
    }
}
