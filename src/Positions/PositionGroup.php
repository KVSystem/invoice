<?php

declare(strict_types=1);

namespace Proengeno\Invoice\Positions;

use ArrayIterator;
use Proengeno\Invoice\Interfaces\Position;
use Proengeno\Invoice\Invoice;
use Proengeno\Invoice\Formatter\Formatter;
use Proengeno\Invoice\Interfaces\InvoiceArray;
use Proengeno\Invoice\Collections\PositionCollection;

class PositionGroup implements InvoiceArray
{
    const NET = 'net';
    const GROSS = 'gross';

    /** @var self::NET|self::GROSS $type */
    private string $type;

    private PositionCollection $positions;

    private float $vatPercent;

    private ?Formatter $formatter = null;

    /**
     * @param self::NET|self::GROSS $type
     * @param array<Position> $positions
     **/
    public function __construct(string $type, float $vatPercent, array $positions)
    {
        $this->type = $type;
        $this->vatPercent = $vatPercent;
        $this->positions = new PositionCollection(...$positions);
    }

    public static function fromArray(array $attributes): self
    {
        extract($attributes);

        $positionsArray = [];
        foreach ($positions as $className => $attributesArray) {
            foreach ($attributesArray as $attributes) {
                $positionsArray[] = $className::fromArray($attributes);
            }
        }

        return new self($type, $vatPercent, $positionsArray);
    }

    /** @param string|array|callable $condition */
    public function only($condition): self
    {
        $instance = new self($this->type, $this->vatPercent, $this->positions->only($condition)->all());
        if ($this->formatter !== null) {
            $instance->setFormatter($this->formatter);
        }
        return $instance;
    }

    /** @param string|array|callable $condition */
    public function except($condition): self
    {
        $instance = new self($this->type, $this->vatPercent, $this->positions->except($condition)->all());
        if ($this->formatter !== null) {
            $instance->setFormatter($this->formatter);
        }
        return $instance;
    }

    public function setFormatter(Formatter $formatter): void
    {
        $this->positions->setFormatter($formatter);

        $this->formatter = $formatter;
    }

    public function format(string $method, array $attributes = []): string
    {
        if ($this->formatter === null) {
            return (string)$this->$method();
        }
        return $this->formatter->format($this, $method, $attributes);
    }

    public function isNet(): bool
    {
        return $this->type === self::NET;
    }

    public function isGross(): bool
    {
        return $this->type === self::GROSS;
    }

    public function hasVat(): bool
    {
        return $this->vatPercent !== 0.0;
    }

    public function vatPercent(): float
    {
        return $this->vatPercent;
    }

    public function positions(): PositionCollection
    {
        return $this->positions;
    }

    public function grossAmount(): float
    {
        if ($this->isGross()) {
            return $this->amount();
        }

        return Invoice::getCalulator()->add($this->netAmount(), $this->vatAmount());
    }

    public function netAmount(): float
    {
        if ($this->isNet()) {
            return $this->amount();
        }

        return round(Invoice::getCalulator()->divide(
            $this->grossAmount(), Invoice::getCalulator()->add(Invoice::getCalulator()->divide($this->vatPercent, 100), 1)
        ), 2);
    }

    public function vatAmount(): float
    {
        if ($this->hasVat() === false) {
            return 0;
        }

        if ($this->isNet()) {
            return round(Invoice::getCalulator()->divide(
                Invoice::getCalulator()->multiply($this->netAmount(), $this->vatPercent), 100
            ), 2);
        }

        return Invoice::getCalulator()->subtract($this->grossAmount(), $this->netAmount());
    }

    public function getIterator(): ArrayIterator
    {
        return $this->positions->getIterator();
    }

    public function offsetExists($offset): bool
    {
        return $this->positions->offsetExists($offset);
    }

    public function offsetGet($offset): Position
    {
        return $this->positions->offsetGet($offset);
    }

    public function offsetSet($offset, $value): void
    {
        $this->positions->offsetSet($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        $this->positions->offsetUnset($offset);
    }

    public function count(): int
    {
        return $this->positions->count();
    }

    public function isEmpty(): bool
    {
        return $this->positions->isEmpty();
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
            'vatPercent' => $this->vatPercent,
            'positions' => $this->positions->jsonSerialize()
        ];
    }

    private function amount(): float
    {
        return $this->positions->sumAmount();
    }
}
