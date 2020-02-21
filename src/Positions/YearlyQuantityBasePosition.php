<?php

namespace Proengeno\Invoice\Positions;

use DateTime;

class YearlyQuantityBasePosition extends PeriodPosition
{
    public function __construct(string $name, DateTime $from, DateTime $until, float $quantity, float $price)
    {
        $this->quantity = $quantity;
        parent::__construct($from, $until, new Position($name, $price, self::calculateQuantity($from, $until, $quantity)));
    }

    private static function calculateQuantity(DateTime $from, DateTime $until, $quantity)
    {
        $days = $until()->format('L') ? 366 : 365;
        return round(bcmul(bcmul(bcdiv(1, $days, 16), $until->diff($from)->days + 1, 16), $quantity, 14), 13);
    }

    public function quantity(): float
    {
        return $this->quantity;
    }

    public function yearlyAmount(): int
    {
        return $this->amount() * ($this->until()->format('L') ? 366 : 365);
    }
}
