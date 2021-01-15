<?php

namespace Proengeno\Invoice\Positions;

use Proengeno\Invoice\Formatter\Formatter;
use Proengeno\Invoice\Invoice;
use Proengeno\Invoice\Interfaces\Position as PositionInterface;

abstract class AbstractPosition implements PositionInterface
{
    protected string $name;
    protected float $quantity;
    protected float $price;
    private ?Formatter $formatter = null;

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

    public function setFormatter(Formatter $formatter = null): void
    {
        $this->formatter = $formatter;
    }

    public function format(string $method, array $attributes = []): string
    {
        if ($this->formatter === null) {
            return (string)$this->$method();
        }
        return $this->formatter->format($this, $method, $attributes);
    }

    abstract public function jsonSerialize(): array;
}
