<?php

declare(strict_types=1);

namespace Proengeno\Invoice\Collections;

use ArrayIterator;
use Exception;
use InvalidArgumentException;
use Proengeno\Invoice\Collections\Collection;
use Proengeno\Invoice\Invoice;
use Proengeno\Invoice\Interfaces\Position;
use Proengeno\Invoice\Formatter\Formatter;
use Proengeno\Invoice\Interfaces\InvoiceArray;
use Proengeno\Invoice\Collections\PositionCollection;
use ReflectionClass;

class PositionCollection implements InvoiceArray
{
    /** @var Collection<Position> */
    private Collection $positions;

    private ?Formatter $formatter = null;

    public function __construct(Position ...$positions)
    {
        $this->positions = new Collection($positions);
    }

    /** @param array<class-string<Position>, array> $positionsArray */
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

    /** @param Collection<Position> $positions */
    private function cloneWithPositions(Collection $positions): self
    {
        $snapshotPositions = $this->positions;
        $this->positions = $positions;
        $instance = clone($this);
        $this->positions = $snapshotPositions;

        return $instance;
    }

    public function setFormatter(Formatter $formatter = null): void
    {
        $this->formatter = $formatter;

        if ($formatter === null) {
            return;
        }

        foreach ($this->positions as $position) {
            $position->setFormatter($formatter);
        }
    }

    public function format(string $method, array $attributes = []): string
    {
        if ($this->formatter === null) {
            return (string)$this->$method();
        }
        return $this->formatter->format($this, $method, $attributes);
    }

    /**
     * @return array<array-key, Position>
     */
    public function all(): array
    {
        return $this->positions->all();
    }

    public function merge(PositionCollection $positions): self
    {
        return $this->cloneWithPositions(
            $this->positions->merge($positions->all())
        );
    }

    /** @param string|array|callable $condition */
    public function only($condition): self
    {
        return $this->cloneWithPositions(
            $this->positions->filter(
                fn(Position $position): mixed => $this->buildClosure($condition)($position)
            )
        );
    }

    /** @param string|array|callable $condition */
    public function except($condition): self
    {
        return $this->cloneWithPositions(
            $this->positions->filter(
                fn(Position $position): bool => ! $this->buildClosure($condition)($position)
            )
        );
    }

    public function sort(callable $callback, bool $descending = false, int $options = SORT_REGULAR): self
    {
        return $this->cloneWithPositions(
            $this->positions->sort($callback, $descending, $options),
        );
    }

    /**
     * @param string|callable $condition
     *
     * @return array<array-key, PositionCollection>
     */
    public function group($condition): array
    {
        $groups = [];

        if (! is_callable($condition)) {
            $condition = fn(Position $pos): mixed => $pos->$condition();
        }

        $preGroups = $this->positions->group($condition);
        foreach ($preGroups as $key => $positions) {
            $groups[$key] = $this->cloneWithPositions($positions);
        }

        return $groups;
    }

    public function sumAmount(): float
    {
        return $this->sum('amount');
    }

    public function sum(string $key): float
    {
        return $this->positions->reduce(function(float $amount, Position $position) use ($key): float {
            return Invoice::getCalulator()->add($amount, (float)$position->$key());
        }, 0.0);
    }

    /** @return mixed */
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

    /** @return mixed */
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

    /** @return ArrayIterator<array-key, Position> */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->positions->all());
    }

    /** @param int $offset */
    public function offsetExists($offset): bool
    {
        return $this->positions->offsetExists($offset);
    }

    /** @param int $offset */
    public function offsetGet($offset): Position
    {
        if (! $this->offsetExists($offset)) {
            throw new Exception(PositionCollection::class . " $offset doesn't exists.");
        }

        $position = $this->positions->offsetGet($offset);

        if ($this->formatter !== null) {
            $position->setFormatter($this->formatter);
        }

        return $position;
    }

    public function offsetSet($offset, $value): void
    {
        throw new Exception(PositionCollection::class . " is immutable.");
    }

    public function offsetUnset($offset): void
    {
        throw new Exception(PositionCollection::class . " is immutable.");
    }

    public function count(): int
    {
        return $this->positions->count();
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function jsonSerialize(): array
    {
        $array = [];
        foreach ($this->positions as $position) {
            $array[get_class($position)][] = $position->jsonSerialize();
        }
        return $array;
    }

    private function buildClosure(string|array|callable $condition): callable
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
