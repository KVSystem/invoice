<?php

declare(strict_types=1);

namespace Proengeno\Invoice\Collections;

use ArrayIterator;
use Exception;
use Proengeno\Invoice\Collections\Collection;
use Proengeno\Invoice\Formatter\Formatter;
use Proengeno\Invoice\Interfaces\InvoiceArray;
use Proengeno\Invoice\Invoice;
use Proengeno\Invoice\Positions\PositionGroup;

class GroupCollection implements InvoiceArray
{
    /** @var Collection<PositionGroup> */
    private Collection $groups;

    private ?Formatter $formatter = null;

    public function __construct(PositionGroup ...$groups)
    {
        $this->groups = new Collection($groups);
    }

    /** @param array<array-key, PositionGroup> $groups */
    public static function fromArray(array $groups): self
    {
        return new GroupCollection(...$groups);
    }

    private function cloneWithGroups(Collection $groups): self
    {
        $snapshotPositions = $this->groups;
        $this->groups = $groups;
        $instance = clone($this);
        $this->groups = $snapshotPositions;

        return $instance;
    }

    public function setFormatter(Formatter|null $formatter = null): void
    {
        $this->formatter = $formatter;

        if ($formatter === null) {
            return;
        }

        foreach ($this->groups as $group) {
            $group->setFormatter($formatter);
        }
    }

    public function format(string $method, array $attributes = []): string
    {
        if ($this->formatter === null) {
            return (string)$this->$method();
        }
        return $this->formatter->format($this, $method, $attributes);
    }

    public function all(): array
    {
        return $this->groups->all();
    }

    /** @param callable(mixed, mixed=):scalar $condition */
    public function filter(callable $condition): self
    {
        return $this->cloneWithGroups($this->groups->filter($condition));
    }

    public function sort(callable $callback, bool $descending = false, int $options = SORT_REGULAR): self
    {
        return $this->cloneWithGroups($this->groups->sort($callback, $descending, $options));
    }

    public function group(callable $callback): array
    {
        return array_map(
            fn(Collection $collection): self => $this->cloneWithGroups($collection), $this->groups->group($callback)
        );
    }

    public function map(callable $callback): array
    {
        return $this->groups->map($callback);
    }

    public function sumGrossAmount(): float
    {
        return $this->groups->reduce(function(float $amount, PositionGroup $group): float {
            return Invoice::getCalulator()->add($amount, $group->grossAmount());
        }, 0.0);
    }

    public function sumNetAmount(): float
    {
        return $this->groups->reduce(function(float $amount, PositionGroup $group): float {
            return Invoice::getCalulator()->add($amount, $group->netAmount());
        }, 0.0);
    }

    public function sumVatAmount(): float
    {
        return $this->groups->reduce(function(float $amount, PositionGroup $group): float {
            return Invoice::getCalulator()->add($amount, $group->vatAmount());
        }, 0.0);
    }

    /** @return ArrayIterator<array-key, PositionGroup> */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->groups->all());
    }

    /** @param int $offset */
    public function offsetExists($offset): bool
    {
        return $this->groups->offsetExists($offset);
    }

    /** @param int $offset */
    public function offsetGet($offset): PositionGroup
    {
        return $this->getIterator()->offsetGet($offset);
    }

    public function offsetSet($offset, $value): void
    {
        throw new Exception(GroupCollection::class . " is immutable.");
    }

    public function offsetUnset($offset): void
    {
        throw new Exception(GroupCollection::class . " is immutable.");
    }

    public function count(): int
    {
        return $this->groups->count();
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function jsonSerialize(): array
    {
        $array = [];
        foreach ($this->groups as $group) {
            $array[] = $group->jsonSerialize();
        }
        return $array;
    }
}
