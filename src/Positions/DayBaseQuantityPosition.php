<?php

namespace Proengeno\Invoice\Positions;

use DateTime;
use InvalidArgumentException;
use Proengeno\Invoice\Invoice;

class DayBaseQuantityPosition extends AbstractPeriodPosition
{
    public function __construct(string $name, float $price, DateTime $from, DateTime $until, float $quantity)
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

    /**
     * @param array{name: string, price: float, from: string, until: string, quantity: float} $attributes
     */
    public static function fromArray(array $attributes): self
    {
        return new self(
            $attributes['name'],
            $attributes['price'],
            new DateTime($attributes['from']),
            new DateTime($attributes['until']),
            $attributes['quantity'],
        );
    }

    public function price(): float
    {
        return round($this->unroundedAmount() / $this->days(), 2);
    }

    public function amount(): float
    {
        return round($this->unroundedAmount(), 2);
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name(),
            'price' => $this->price(),
            'amount' => $this->amount(),
            'from' => $this->from()->format('Y-m-d H:i:s'),
            'until' => $this->until()->format('Y-m-d H:i:s'),
            'quantity' => $this->quantity(),
        ];
    }

    private function unroundedAmount(): float
    {
        return Invoice::getCalulator()->multiply(
            Invoice::getCalulator()->multiply($this->quantity, $this->days()),
            $this->price
        );
    }
}
