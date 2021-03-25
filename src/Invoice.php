<?php

declare(strict_types=1);

namespace Proengeno\Invoice;

use Proengeno\Invoice\Collections\GroupCollection;
use Proengeno\Invoice\Formatter\Formatter;
use Proengeno\Invoice\Interfaces\Calculator;
use Proengeno\Invoice\Interfaces\Formatable;
use Proengeno\Invoice\Positions\PositionGroup;
use Proengeno\Invoice\Calculator\BcMathCalculator;
use Proengeno\Invoice\Collections\PositionCollection;

class Invoice implements \JsonSerializable, Formatable
{
    private static ?Calculator $calculator = null;
    protected GroupCollection $positionGroups;
    private ?Formatter $formatter = null;

    public function __construct(array $positionGroups)
    {
        $this->positionGroups = new GroupCollection(...$positionGroups);
    }

    public static function fromArray(array $positionsGroupsArray): self
    {
        $positionGroups = [];

        foreach ($positionsGroupsArray as $positionGroup) {
            $positionGroups[] = PositionGroup::fromArray($positionGroup);
        }

        return new self($positionGroups);
    }

    public static function negateFromArray(array $positionsGroupsArray): self
    {
        foreach ($positionsGroupsArray as $positionGroupKey => $positionGroup) {
            foreach ($positionGroup['positions'] ?? [] as $positionClass => $positions) {
                foreach (array_keys($positions) as $positionKey) {
                    $positionsGroupsArray[$positionGroupKey]['positions'][$positionClass][$positionKey]['price'] *= -1;
                }
            }
        }

        return self::fromArray($positionsGroupsArray);
    }

    public static function getCalulator(): Calculator
    {
        if (null !== self::$calculator) {
            return self::$calculator;
        }

        return self::$calculator = new BcMathCalculator;
    }

    public static function setCalulator(Calculator $calculator): void
    {
        self::$calculator = $calculator;
    }

    public function negate(): self
    {
        $nagation = self::negateFromArray($this->jsonSerialize());

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

    public function format(string $method, array $attributes = []): string
    {
        if ($this->formatter === null) {
            return (string)$this->$method();
        }
        return $this->formatter->format($this, $method, $attributes);
    }

    /** @deprecated */
    public function positionGroups(): array
    {
        return $this->positionGroups->all();
    }

    public function groups(): GroupCollection
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
        return $this->positionGroups->sumNetAmount();
    }

    public function vatAmount(): float
    {
        return $this->positionGroups->sumVatAmount();
    }

    public function grossAmount(): float
    {
        return $this->positionGroups->sumGrossAmount();
    }

    public function jsonSerialize(): array
    {
        return $this->positionGroups->jsonSerialize();
    }

    /** @param string|array|callable|null $condition */
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
