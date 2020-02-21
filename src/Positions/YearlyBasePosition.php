<?php

namespace Proengeno\Invoice\Positions;

use DateTime;

class YearlyBasePosition extends PeriodPosition
{
    public function __construct(string $name, DateTime $from, DateTime $until, float $price)
    {
        parent::__construct($from, $until, new Position($name, $price, self::calculateQuantity($from, $until)));
    }

    private static function calculateQuantity(DateTime $from, DateTime $until)
    {
        $days = $until()->format('L') ? 366 : 365;
        return round(bcmul(bcdiv(1, $days, 16), ($until->diff($from)->days + 1), 16), 13);
    }

    public function yearlyAmount(): int
    {
        return $this->amount() * ($this->until()->format('L') ? 366 : 365);
    }
}
