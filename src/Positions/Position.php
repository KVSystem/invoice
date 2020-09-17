<?php

namespace Proengeno\Invoice\Positions;

use Proengeno\Invoice\Invoice;
use Proengeno\Invoice\Formatter\FormatableTrait;
use Proengeno\Invoice\Interfaces\Position as PositionInterface;

class Position implements PositionInterface
{
    use FormatableTrait;

    private $name;
    private $quantity;
    private $price;

    public function __construct(string $name, float $price, float $quantity)
    {
        $this->name = $name;
        $this->quantity = $quantity;
        $this->price = $price;
    }

    public static function fromArray(array $attributes)
    {
        extract($attributes);

        return new static($name, $price, $quantity);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function price(): float
    {
        return $this->price;
    }

    public function quantity(): float
    {
        return $this->quantity;
    }

    public function amount(): float
    {
        return round(
            Invoice::getCalulator()->multiply($this->price, $this->quantity), 2
        );
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
