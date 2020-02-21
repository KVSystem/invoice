<?php

namespace Proengeno\Invoice\Positions;

use DateTime;
use Proengeno\Invoice\Positions\Position;
use Proengeno\Invoice\Formatter\FormatableTrait;

class MonthlyBasePosition implements PeriodPositionInterface
{
    use FormatableTrait;

    private $corePosition;

    public function __construct(string $name, DateTime $from, DateTime $until, float $price)
    {
        $this->from = $from;
        $this->until = $until;
        $this->corePosition = new Position($name, $price, self::calculateQuantity($from, $until));
    }

    private static function calculateQuantity(DateTime $from, DateTime $until)
    {
        return round(bcmul(bcdiv(12, 365, 16), ($until->diff($from)->days + 1), 16), 13);
    }

    public function name(): string
    {
        return $this->corePosition->name();
    }

    public function from(): DateTime
    {
        return $this->from;
    }

    public function until(): DateTime
    {
        return $this->until;
    }

    public function price(): float
    {
        return $this->corePosition->price();
    }

    public function quantity(): float
    {
        return $this->from()->diff($this->until())->days + 1;
    }

    public function amount(): int
    {
        return $this->corePosition->amount();
    }

    public function yearlyAmount(): int
    {
        return $this->amount() * ($this->until()->format('L') ? 366 : 365);
    }

    public function jsonSerialize()
    {
        return array_merge($this->corePosition->jsonSerialize(), [
            'from' => $this->from()->format('Y-m-d'),
            'until' => $this->until()->format('Y-m-d'),
        ]);
    }
}
