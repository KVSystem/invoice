<?php

namespace Proengeno\Invoice\Positions;

use Proengeno\Invoice\Formatter\Formatter;
use Proengeno\Invoice\Formatter\Formatable;
use Proengeno\Invoice\Formatter\FormatIterator;
use Proengeno\Invoice\Formatter\FormatableTrait;
use Proengeno\Invoice\Formatter\PositionIterator;
use Proengeno\Invoice\Positions\PositionInterface;

class PositionCollection implements \ArrayAccess, \IteratorAggregate, Formatable
{
    use FormatableTrait;

    private $positions = [];

    public function __construct(PositionInterface ...$positions)
    {
        $this->positions = $positions;
    }

    public static function createWithFormatter(array $positions, Formatter $formatter = null)
    {
        $instance = new static(...$positions);
        $instance->setFormatter($formatter);

        return $instance;
    }

    public function add(PositionInterface $position): void
    {
        $this->positions[] = $position;
    }

    public function all(): array
    {
        return $this->positions;
    }

    public function merge(PositionCollection $positions): self
    {
        return static::createWithFormatter(
            array_merge($this->positions, $positions->all()),
            $this->formatter
        );
    }

    public function only($name): self
    {
        if (!is_array($name)) {
            $name = [$name];
        }
        return static::createWithFormatter(
            array_filter($this->positions, function($position) use ($name) {
                return in_array($position->name(), $name);
            }),
            $this->formatter
        );
    }

    public function except(string $name): self
    {
        return static::createWithFormatter(
            array_filter($this->positions, function($position) use ($name) {
                return $position->name() != $name;
            }),
            $this->formatter
        );
    }

    public function group(): array
    {
        $results = [];
        foreach ($this->positions as $position) {
            if (! array_key_exists($position->name(), $results)) {
                $results[$position->name()] = static::createWithFormatter([$position], $this->formatter);
                continue;
            }
            $results[$position->name()]->add($position);
        }
        return $results;
    }

    public function sumAmount(): int
    {
        return array_reduce($this->positions, function($amount, $position) {
            return $amount + $position->amount();
        }, 0);
    }

    public function getIterator(): PositionIterator
    {
        return new PositionIterator($this->positions, $this->formatter);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->positions[$offset]);
    }

    public function offsetGet($offset)
    {
        $position = $this->getIterator()->offsetGet($offset);
        $position->setFormatter($this->formatter);

        return $position;
    }

    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->positions[] = $value;
        } else {
            $this->positions[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->positions[$offset]);
    }

    public function count(): int
    {
        return count($this->positions);
    }

    public function isEmpty(): bool
    {
        return count($this->positions) === 0;
    }
}
