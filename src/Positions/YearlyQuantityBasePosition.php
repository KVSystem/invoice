<?php

namespace Proengeno\Invoice\Positions;

use DateTime;
use Proengeno\Invoice\Formatter\FormatableTrait;

class YearlyQuantityBasePosition implements PeriodPositionInterface
{
    use FormatableTrait;

    private $corePosition;

    public function __construct(string $name, DateTime $from, DateTime $until, float $quantity, float $price)
    {
        $this->from = $from;
        $this->until = $until;
        $this->quantity = $quantity;
        $this->corePosition = new Position($name, $price, self::calculateQuantity($from, $until, $quantity));
    }

    private static function calculateQuantity(DateTime $from, DateTime $until, $quantity)
    {
        $days = $until()->format('L') ? 366 : 365;
        return round(bcmul(bcmul(bcdiv(1, $days, 16), $until->diff($from)->days + 1, 16), $quantity, 14), 13);
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
        return $this->quantity;
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
