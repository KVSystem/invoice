<?php

namespace Proengeno\Invoice\Positions;

use Proengeno\Invoice\Formatter\Formatable;
use Proengeno\Invoice\Formatter\FormatableTrait;
use Proengeno\Invoice\Positions\PositionInterface;
use Proengeno\Invoice\Formatter\Formatter;

class PositionGroup implements \ArrayAccess, \IteratorAggregate, Formatable
{
    use FormatableTrait;

    const NET = 'net';
    const GROSS = 'gross';

    private $type;
    private $vatPercent;
    private $positions = [];

    public function __construct(string $type, float $vatPercent, array $positions)
    {
        $this->type = $type;
        $this->vatPercent = $vatPercent;
        $this->positions = new PositionCollection(...$positions);
        if ($this->hasVat()) {
            $this->vatMultiplier = (float)bcdiv($this->vatPercent, 100, 4);
        }
    }

    public function setFormatter(Formatter $formatter): void
    {
        $this->formatter = $formatter;
        $this->positions->setFormatter($formatter);
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

    public function grossAmount(): int
    {
        if ($this->isGross()) {
            return $this->amount();
        }

        return $this->netAmount() + $this->vatAmount();
    }

    public function netAmount(): int
    {
        if ($this->isNet()) {
            return $this->amount();
        }
        return (int)round(bcmul(bcdiv($this->grossAmount(), bcadd($this->vatPercent, 100, 0), 3), 100, 1), 0);
    }

    public function vatAmount(): int
    {
        if ($this->hasVat() === false) {
            return 0;
        }

        if ($this->isNet()) {
            return (int)round(bcdiv(bcmul($this->netAmount(), $this->vatPercent, 0), 100, 1), 0);
        }

        return $this->grossAmount() - $this->netAmount();
    }

    public function getIterator(): \ArrayIterator
    {
        return $this->positions->getIterator();
    }

    public function offsetExists($offset): bool
    {
        return $this->positions->offsetExists($offset);
    }

    public function offsetGet($offset)
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

    private function amount(): int
    {
        return $this->positions->sumAmount();
    }
}
