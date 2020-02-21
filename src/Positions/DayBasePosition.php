<?php

namespace Proengeno\Invoice\Positions;

use DateTime;
use Proengeno\Invoice\Positions\Position;
use Proengeno\Invoice\Formatter\FormatableTrait;

class DayBasePosition extends PeriodPosition
{
    public function __construct(string $name, DateTime $from, DateTime $until, float $price)
    {
        parent::__construct($from, $until, new Position($name, $price, self::calculateQuantity($from, $until)));
    }

    private static function calculateQuantity(DateTime $from, DateTime $until)
    {
        return $until->diff($from)->days + 1;
    }

    public function yearlyAmount(): int
    {
        return $this->amount() * ($this->until()->format('L') ? 366 : 365);
    }
}
