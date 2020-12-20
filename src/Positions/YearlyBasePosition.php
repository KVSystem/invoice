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

    /** @return static */
    public static function fromArray(array $attributes)
    {
        extract($attributes);

        return new static($name, $price, new DateTime($from), new DateTime($until));
    }

    private static function calculateQuantity(DateTime $from, DateTime $until): float
    {
        return round(Invoice::getCalulator()->multiply(
            self::getYearlyFactor($from, $until), $until->diff($from)->days + 1
        ), 13);
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
