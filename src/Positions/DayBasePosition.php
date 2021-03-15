<?php

namespace Proengeno\Invoice\Positions;

use DateTime;
use InvalidArgumentException;
use Proengeno\Invoice\Invoice;

class DayBasePosition extends AbstractPeriodPosition
{
    public function __construct(string $name, float $price, DateTime $from, DateTime $until)
    {
        if ($until->format('Ymd') < $from->format('Ymd')) {
            throw new InvalidArgumentException($until->format('Y-m-d') . ' must be greater/equal than ' . $from->format('Y-m-d'));
        }
        $this->name = $name;
        $this->quantity = self::calculateQuantity($from, $until);
        $this->price = $price;
        $this->from = $from;
        $this->until = $until;
    }

    /**
     * @psalm-param array{name: string, price: float, from: string, until: string} $attributes
     */
    public static function fromArray(array $attributes): self
    {
        return new self(
            $attributes['name'],
            $attributes['price'],
            new DateTime($attributes['from']),
            new DateTime($attributes['until'])
        );
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
