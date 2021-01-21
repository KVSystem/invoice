<?php

declare(strict_types=1);

namespace Proengeno\Invoice\Collections;

use Closure;
use Exception;
use ArrayIterator;

/**
 * @internal
 */
final class Collection implements \Countable, \ArrayAccess, \IteratorAggregate
{
    private array $items;

    public function __construct(array $items = [])
    {
        return $this->items = $items;
    }

    private function cloneWithItems(array $items = []): Collection
    {
        $instance = clone $this;
        $instance->items = $items;

        return $instance;
    }

    /** @param mixed $item */
    public function add($item): void
    {
        $this->items[] = $item;
    }

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

    public function merge(array $items): Collection
    {
        return new Collection(array_merge($this->items, $items));
    }

    public function filter(callable $callback): Collection
    {
        return new Collection(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
    }

    public function map(callable $callback): Collection
    {
        $keys = array_keys($this->items);

        $items = array_map($callback, $this->items, $keys);

        return new Collection(array_combine($keys, $items));
    }

    /**
     * @param int|float $initial
     * @return int|float
     */
    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->items, $callback, $initial);
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

    public function group(Callable $groupBy, bool $preserveKeys = false): Collection
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

                $results[$groupKey]->offsetSet($preserveKeys ? $key : null, $value);
            }
        }

        return new Collection($results);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * @param int $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    /**
     * @param int|null $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    /** @param int $offset */
    public function offsetUnset($offset): void
    {
        if ($this->offsetExists($offset)) {
            unset($this->items[$offset]);
        }
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }
}
