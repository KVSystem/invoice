<?php

namespace Proengeno\Invoice\Positions;

use ReflectionClass;
use InvalidArgumentException;
use Proengeno\Invoice\Interfaces\Position;
use Proengeno\Invoice\Formatter\Formatter;
use Proengeno\Invoice\Interfaces\InvoiceArray;
use Proengeno\Invoice\Formatter\FormatableTrait;
use Proengeno\Invoice\Formatter\PositionIterator;

class PositionCollection implements InvoiceArray
{
    use FormatableTrait;

    private $positions = [];

    public function __construct(Position ...$positions)
    {
        $this->positions = $positions;
    }

    public static function fromArray(array $positionsArray): self
    {
        $positions = [];
        foreach ($positionsArray as $positionClass => $attributesArray) {
            foreach ($attributesArray as $attributes) {
                $positions[] = self::newPosition($positionClass, $attributes);
            }
        }

        return new self(...$positions);
    }

    public static function createWithFormatter(array $positions, Formatter $formatter = null): self
    {
        $instance = new self(...$positions);
        $instance->setFormatter($formatter);

        return $instance;
    }

    public function add(Position $position): void
    {
        $this->positions[] = $position;
    }

    public function all(): array
    {
        if ($this->formatter === null) {
            return $this->positions;
        }

        $positions = [];
        foreach ($this as $position) {
            $positions[] = $position;
        }

        return $positions;
    }

    public function merge(PositionCollection $positions): self
    {
        return self::createWithFormatter(
            array_merge($this->positions, $positions->all()),
            $this->formatter
        );
    }

    public function only($condition): self
    {
        return self::createWithFormatter(
            array_filter($this->positions, function(Position $position) use ($condition) {
                return $this->buildClosure($condition)($position);
            }),
            $this->formatter
        );
    }

    public function except($condition): self
    {
        return self::createWithFormatter(
            array_filter($this->positions, function(Position $position) use ($condition) {
                return !$this->buildClosure($condition)($position);
            }),
            $this->formatter
        );
    }

    public function group(string $key): array
    {
        $results = [];
        foreach ($this->positions as $position) {
            if (! array_key_exists((string)$position->$key(), $results)) {
                $results[(string)$position->$key()] = static::createWithFormatter([$position], $this->formatter);
                continue;
            }
            $results[(string)$position->$key()]->add($position);
        }
        return $results;
    }

    public function sumAmount(): int
    {
        return $this->sum('amount');
    }

    public function sum(string $key)
    {
        return array_reduce($this->positions, function($amount, Position $position) use ($key) {
            return $amount + $position->$key();
        }, 0);
    }

    public function min(string $key)
    {
        $min = null;

        foreach ($this->positions as $position) {
            if ($min === null || $position->$key() < $min) {
                $min = $position->$key();
            }
        }

        return $min;
    }

    public function max(string $key)
    {
        $max = null;

        foreach ($this->positions as $position) {
            if ($max === null || $position->$key() > $max) {
                $max = $position->$key();
            }
        }

        return $max;
    }

    public function getIterator(): PositionIterator
    {
        return new PositionIterator($this->positions, $this->formatter);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->positions[$offset]);
    }

    public function offsetGet($offset): Position
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

    public function jsonSerialize(): array
    {
        $array = [];
        foreach ($this->positions as $position) {
            $array[get_class($position)][] = $position->jsonSerialize();
        }
        return $array;
    }

    private function buildClosure($condition): callable
    {
        if (is_callable($condition)) {
            return $condition;
        }

        return function (Position $position) use ($condition) {
            if (!is_array($condition)) {
                $condition = [$condition];
            }
            return in_array($position->name(), $condition);
        };
    }

    private static function newPosition(string $positionClass, array $attributes): Position
    {
        if (class_exists($positionClass)) {
            if ((new ReflectionClass($positionClass))->implementsInterface(Position::class) ) {
                return $positionClass::fromArray($attributes);
            }
            throw new InvalidArgumentException("$positionClass doesn't implement '" . Position::class . "' interface");
        }
        throw new InvalidArgumentException("$positionClass doesn't exists");
    }
}
