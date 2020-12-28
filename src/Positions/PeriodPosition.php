<?php

namespace Proengeno\Invoice\Positions;

use DateTime;
use InvalidArgumentException;

class PeriodPosition extends AbstractPeriodPosition
{
    public function __construct(string $name, float $price, float $quantity, DateTime $from, DateTime $until)
    {
        if ($until->format('Ymd') < $from->format('Ymd')) {
            throw new InvalidArgumentException($until->format('Y-m-d') . ' must be greater/equal than ' . $from->format('Y-m-d'));
        }
        $this->name = $name;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->from = $from;
        $this->until = $until;
    }

    public static function fromArray(array $attributes): self
    {
        extract($attributes);

        return new self($name, $price, $quantity, new DateTime($from), new DateTime($until));
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name(),
            'price' => $this->price(),
            'amount' => $this->amount(),
            'quantity' => $this->quantity(),
            'from' => $this->from()->format('Y-m-d H:i:s'),
            'until' => $this->until()->format('Y-m-d H:i:s'),
        ];
    }
}
