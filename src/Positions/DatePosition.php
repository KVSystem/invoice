<?php

namespace Proengeno\Invoice\Positions;

use DateTime;

class DatePosition extends AbstractPosition
{
    private DateTime $date;

    public function __construct(string $name, float $price, float $quantity, DateTime $date)
    {
        $this->name = $name;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->date = $date;
    }

    /**
     * @psalm-param array{name: string, price: float, amount: float, quantity: float, date:string} $attributes
     */
    public static function fromArray(array $attributes): self
    {
        return new self($attributes['name'], $attributes['price'], $attributes['quantity'], new DateTime($attributes['date']));
    }

    public function date(): DateTime
    {
        return $this->date;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name(),
            'price' => $this->price(),
            'amount' => $this->amount(),
            'quantity' => $this->quantity(),
            'date' => $this->date()->format('Y-m-d'),
        ];
    }
}
