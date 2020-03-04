<?php

namespace Proengeno\Invoice;

use Proengeno\Invoice\Formatter\Formatter;
use Proengeno\Invoice\Interfaces\Formatable;
use Proengeno\Invoice\Positions\PositionGroup;
use Proengeno\Invoice\Formatter\FormatableTrait;
use Proengeno\Invoice\Positions\PositionCollection;

class Invoice implements \JsonSerializable, Formatable
{
    use FormatableTrait;

    protected $positionGroups;

    public function __construct(PositionGroup ...$positionGroups)
    {
        $this->positionGroups = $positionGroups;
    }

    public static function fromArray(array $positionsGroupsArray)
    {
        $positionsGroups = [];

        foreach ($positionsGroupsArray as $positionGroups) {
            $positionsGroups[] = PositionGroup::fromArray($positionGroups);
        }

        return new static(...$positionsGroups);
    }

    public function setFormatter(Formatter $formatter): void
    {
        $this->formatter = $formatter;
        foreach ($this->positionGroups as $positionGroup) {
            $positionGroup->setFormatter($formatter);
        }
    }

    public function positionGroups(): array
    {
        return $this->positionGroups;
    }

    public function netPositions(string $name = null): PositionCollection
    {
        return $this->filterPositions('isNet', $name);
    }

    public function grossPositions(string $name = null): PositionCollection
    {
        return $this->filterPositions('isGross', $name);
    }

    public function netAmount(): int
    {
        return $this->sum('netAmount');
    }

    public function vatAmount(): int
    {
        return $this->sum('vatAmount');
    }

    public function grossAmount(): int
    {
        return $this->sum('grossAmount');
    }

    public function jsonSerialize(): array
    {
        $array = [];
        foreach ($this->positionGroups as $positionGroup) {
            $array[] = $positionGroup->jsonSerialize();
        }
        return $array;
    }

    private function sum($method): int
    {
        return array_reduce($this->positionGroups, function($total, $positionGroup) use ($method) {
            return $total + $positionGroup->$method();
        }, 0);
    }

    private function filterPositions($vatType, $name)
    {
        $positions = new PositionCollection;
        $positions->setFormatter($this->formatter);
        foreach ($this->positionGroups as $positionGroup) {
            if ($positionGroup->$vatType()) {
                if (null === $name) {
                    $positions = $positions->merge($positionGroup->positions());
                } else {
                    $positions = $positions->merge($positionGroup->positions()->only($name));
                }
            }
        }
        return $positions;
    }
}
