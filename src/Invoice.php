<?php

namespace Proengeno\Invoice;

use Proengeno\Invoice\Formatter\Formatter;
use Proengeno\Invoice\Interfaces\Calculator;
use Proengeno\Invoice\Interfaces\Formatable;
use Proengeno\Invoice\Positions\PositionGroup;
use Proengeno\Invoice\Formatter\FormatableTrait;
use Proengeno\Invoice\Calculator\BcMathCalculator;
use Proengeno\Invoice\Positions\PositionCollection;

class Invoice implements \JsonSerializable, Formatable
{
    use FormatableTrait;

    private static $calculator;
    protected $positionGroups;

    public function __construct(PositionGroup ...$positionGroups)
    {
        $this->positionGroups = $positionGroups;
    }

    public static function fromArray(array $positionsGroupsArray)
    {
        $positionGroups = [];

        foreach ($positionsGroupsArray as $positionGroup) {
            $positionGroups[] = PositionGroup::fromArray($positionGroup);
        }

        return new static(...$positionGroups);
    }

    public static function negateFromArray(array $positionsGroupsArray)
    {
        foreach ($positionsGroupsArray as $positionGroupKey => $positionGroup) {
            foreach ($positionGroup['positions'] ?? [] as $positionClass => $positions) {
                foreach ($positions as $positionKey => $positionAttributes) {
                    $positionsGroupsArray[$positionGroupKey]['positions'][$positionClass][$positionKey]['price'] *= -1;
                }
            }
        }

        return static::fromArray($positionsGroupsArray);
    }

    public static function getCalulator(): Calculator
    {
        if (null === self::$calculator) {
            self::$calculator = new BcMathCalculator;
        }

        return self::$calculator;
    }

    public static function setCalulator(Calculator $calculator): void
    {
        self::$calculator = $calculator;
    }

    public function negate()
    {
        $nagation = static::negateFromArray($this->jsonSerialize());

        if (null !== $this->formatter) {
            $nagation->setFormatter($this->formatter);
        }

        return $nagation;
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

    /**
     * @param string|array|\Closure $condition
     */
    public function netPositions($condition = null): PositionCollection
    {
        return $this->filterPositions('isNet', $condition);
    }

    /**
     * @param string|array|\Closure $condition
     */
    public function grossPositions($condition = null): PositionCollection
    {
        return $this->filterPositions('isGross', $condition);
    }

    public function netAmount(): float
    {
        return $this->sum('netAmount');
    }

    public function vatAmount(): float
    {
        return $this->sum('vatAmount');
    }

    public function grossAmount(): float
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

    private function sum(string $method): float
    {
        return array_reduce($this->positionGroups, function(float $total, PositionGroup $positionGroup) use ($method): float {
            return self::getCalulator()->add($total, $positionGroup->$method());
        }, 0.0);
    }

    private function filterPositions(string $vatType, $condition = null): PositionCollection
    {
        $positions = new PositionCollection;
        $positions->setFormatter($this->formatter);
        foreach ($this->positionGroups as $positionGroup) {
            if ($positionGroup->$vatType()) {
                if (null === $condition) {
                    $positions = $positions->merge($positionGroup->positions());
                } else {
                    $positions = $positions->merge($positionGroup->positions()->only($condition));
                }
            }
        }
        return $positions;
    }
}
