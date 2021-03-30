<?php

declare(strict_types=1);

namespace Proengeno\Invoice\Collections;

use Closure;
use Exception;
use ArrayIterator;

/**
 * @internal
 *
 * @template T
 */
final class Collection implements \Countable, \IteratorAggregate
{
    /** @var array<array-key, T> */
    private array $items;

    /** @psalm-param array<array-key, T> $items */
    public function __construct(array $items = [])
    {
        return $this->items = $items;
    }

    /** @psalm-param T $item */
    public function add($item): void
    {
        $this->items[] = $item;
    }

    /** @return array<array-key, T> */
    public function all(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /** @psalm-param array<array-key, T> $items */
    public function merge(array $items): Collection
    {
        return new Collection(array_merge($this->items, $items));
    }

    /** @psalm-param callable(mixed, mixed=):scalar $callable */
    public function filter(callable $callable): Collection
    {
        return new Collection(array_values(array_filter($this->items, $callable)));
    }

    public function map(callable $callback): array
    {
        return array_values(array_map($callback, $this->items));
    }

    /** @psalm-param callable(mixed, mixed=):scalar $callable */
    public function reduce(callable $callable, int|float $initial = 0): int|float
    {
        /** @psalm-suppress MixedAssignment */
        $result = array_reduce($this->items, $callable, $initial);

        if (is_int($result) || is_float($result)) {
            return $result;
        }

        throw new Exception("Only integers and float can reduced.");
    }

    public function sort(callable $callback, bool $descending = false, int $options = SORT_REGULAR): Collection
    {
        $results = [];

        // First we will loop through the items and get the comparator from a callback
        // function which we were given. Then, we will sort the returned values and
        // and grab the corresponding values for the sorted keys from this array.
        foreach ($this->items as $key => $value) {
            $results[$key] = $callback($value);
        }

        $descending ? arsort($results, $options)
            : asort($results, $options);

        // Once we have sorted all of the keys in the array, we will loop through them
        // and grab the corresponding model so we can set the underlying items list
        // to the sorted version. Then we'll just return the collection instance.
        foreach (array_keys($results) as $key) {
            $results[$key] = $this->items[$key];
        }

        // Return new Clone and Reindex
        return new Collection(array_values($results));
    }

    /**
     * @retur array<int|string, Collection>
     */
    public function group(Callable $groupBy): array
    {
        $results = [];

        foreach ($this->items as $key => $value) {
            $groupKeys = $groupBy($value, $key);

            if (! is_array($groupKeys)) {
                $groupKeys = [$groupKeys];
            }

            foreach ($groupKeys as $groupKey) {
                if (is_bool($groupKey)) {
                    $groupKey = (int) $groupKey;
                } elseif (is_int($groupKey)) {
                    $groupKey = $groupKey;
                } else {
                    $groupKey = (string)$groupKey;
                }

                if (! array_key_exists($groupKey, $results)) {
                    $results[$groupKey] = new Collection;
                }

                $results[$groupKey]->add($value);
            }
        }

        return $results;
    }

    public function offsetExists(int $offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * @psalm-param int $offset
     *
     * @psalm-return T
     */
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }
}
