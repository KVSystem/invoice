<?php

namespace Proengeno\Invoice\Positions;

class Position extends AbstractPosition
{
    public function __construct(string $name, float $price, float $quantity)
    {
        $this->name = $name;
        $this->quantity = $quantity;
        $this->price = $price;
    }

    public static function fromArray(array $attributes): self
    {
        extract($attributes);

        return new self($name, $price, $quantity);
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name(),
            'price' => $this->price(),
            'amount' => $this->amount(),
            'quantity' => $this->quantity(),
        ];
    }
}
