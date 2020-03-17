<?php

namespace Proengeno\Invoice\Positions;

use DateTime;

class YearlyQuantityBasePosition extends PeriodPosition
{
    private $publicQuantity;

    public function __construct(string $name, float $price, float $quantity, DateTime $from, DateTime $until)
    {
        $this->publicQuantity = $quantity;
        parent::__construct($name, $price, self::calculateQuantity($from, $until, $quantity), $from, $until);
    }

    private static function calculateQuantity(DateTime $from, DateTime $until, $quantity)
    {
        $days = $until->format('L') ? 366 : 365;
        return round(bcmul(bcmul(bcdiv(1, $days, 16), $until->diff($from)->days + 1, 16), $quantity, 14), 13);
    }

    public function quantity(): float
    {
        return $this->publicQuantity;
    }

    public function yearlyAmount(): int
    {
        return $this->amount() * ($this->until()->format('L') ? 366 : 365);
    }
}
