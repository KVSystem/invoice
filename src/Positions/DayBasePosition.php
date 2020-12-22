<?php

namespace Proengeno\Invoice\Positions;

use DateTime;
use InvalidArgumentException;
use Proengeno\Invoice\Invoice;

class DayBasePosition extends AbstractPeriodPosition
{
    public function __construct(string $name, float $price, DateTime $from, DateTime $until)
    {
        if ($until < $from) {
            throw new InvalidArgumentException($until->format('Y-m-d H:i:s') . ' must be greaten than ' . $from->format('Y-m-d H:i:s'));
        }
        $this->name = $name;
        $this->quantity = self::calculateQuantity($from, $until);
        $this->price = $price;
        $this->from = $from;
        $this->until = $until;
    }

    public static function fromArray(array $attributes): self
    {
        extract($attributes);

        return new self($name, $price, new DateTime($from), new DateTime($until));
    }

    private static function calculateQuantity(DateTime $from, DateTime $until): int
    {
        return $until->diff($from)->days + 1;
    }

    public function yearlyAmount(): float
    {
        return Invoice::getCalulator()->multiply(
            $this->amount(), $this->until()->format('L') ? 366 : 365
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name(),
            'price' => $this->price(),
            'amount' => $this->amount(),
            'from' => $this->from()->format('Y-m-d H:i:s'),
            'until' => $this->until()->format('Y-m-d H:i:s'),
        ];
    }
}
