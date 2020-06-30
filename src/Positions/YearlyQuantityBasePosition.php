<?php

namespace Proengeno\Invoice\Positions;

use DateTime;
use Proengeno\Invoice\Invoice;

class YearlyQuantityBasePosition extends PeriodPosition
{
    private $publicQuantity;

    public function __construct(string $name, float $price, float $quantity, DateTime $from, DateTime $until)
    {
        $this->publicQuantity = $quantity;
        parent::__construct($name, $price, self::calculateQuantity($from, $until, $quantity), $from, $until);
    }

    private static function calculateQuantity(DateTime $from, DateTime $until, float $quantity): float
    {
        $days = $until->format('L') ? 366 : 365;

        return round(Invoice::getCalulator()->multiply(
            Invoice::getCalulator()->multiply(
                Invoice::getCalulator()->divide(1, $days), $until->diff($from)->days + 1
            ),
            $quantity
        ), 13);
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
