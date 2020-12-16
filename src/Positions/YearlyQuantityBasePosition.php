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
                self::getYearlyFactor($from, $until), $until->diff($from)->days + 1
            ),
            $quantity
        ), 13);
    }

    public function quantity(): float
    {
        return $this->publicQuantity;
    }

    public function yearlyAmount(): float
    {
        return Invoice::getCalulator()->multiply(
            $this->amount(), $this->until()->format('L') ? 366 : 365
        );
    }

    private static function getYearlyFactor(DateTime $from, DateTime $until): float
    {
        $current = clone $from;

        $leapDays = 0;
        $leapDevider = 1;
        $leapAddition = 0;
        while ($current->modify("last day of feb") && $current->format('Ymd') <= $until->format('Ymd')) {
            if ($current->format('d') == 29 && $current->format('Ymd') >= $from->format('Ymd')) {
                $leapDays++;
            }
            if ($current->format('d') == 28 && $current->format('Ymd') >= $from->format('Ymd')) {
                $leapDevider++;
            }
            $current->modify("+1 year");
        }

        if ($leapDays > 0) {
            $leapAddition = Invoice::getCalulator()->divide($leapDays, $leapDevider);
        }

        return Invoice::getCalulator()->divide(
            1, Invoice::getCalulator()->add(365, $leapAddition)
        );
    }
}
