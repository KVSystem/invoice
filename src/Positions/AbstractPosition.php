<?php

namespace Proengeno\Invoice\Positions;

use Proengeno\Invoice\Invoice;
use Proengeno\Invoice\Formatter\FormatableTrait;
use Proengeno\Invoice\Interfaces\Position as PositionInterface;

abstract class AbstractPosition implements PositionInterface
{
    use FormatableTrait;

    protected string $name;
    protected float $quantity;
    protected float $price;

    abstract static function fromArray(array $attributes): self;

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
            Invoice::getCalulator()->multiply($this->price(), $this->quantity()), 2
        );
    }

    abstract public function jsonSerialize(): array;
}
