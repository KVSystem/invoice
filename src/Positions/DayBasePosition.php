<?php

namespace Proengeno\Invoice\Positions;

use DateTime;
use Proengeno\Invoice\Formatter\FormatableTrait;

class DayBasePosition extends PeriodPosition
{
    public function __construct(string $name, float $price, DateTime $from, DateTime $until)
    {
        parent::__construct($name, $price, self::calculateQuantity($from, $until), $from, $until);
    }

    private static function calculateQuantity(DateTime $from, DateTime $until): int
    {
        return $until->diff($from)->days + 1;
    }

    public function yearlyAmount(): int
    {
        return $this->amount() * ($this->until()->format('L') ? 366 : 365);
    }
}
