<?php

namespace Proengeno\Invoice;

use Proengeno\Invoice\Interfaces\Position;
use Proengeno\Invoice\Positions\PositionGroup;

class Factory
{
    private $netPositions = [];
    private $grossPositions = [];

    public function addNetPosition(float $vatPercent, Position $position): self
    {
        $this->netPositions[$vatPercent][] = $position;

        return $this;
    }

    public function addNetPositionArray(float $vatPercent, array $positions): self
    {
        foreach ($positions as $position) {
            $this->addNetPosition($vatPercent, $position);
        }

        return $this;
    }

    public function addGrossPosition(float $vatPercent, Position $position): self
    {
        $this->grossPositions[$vatPercent][] = $position;

        return $this;
    }

    public function addGrossPositionArray(float $vatPercent, array $positions): self
    {
        foreach ($positions as $position) {
            $this->addGrossPosition($vatPercent, $position);
        }

        return $this;
    }

    public function build(): Invoice
    {
        $positionGroups = [];

        foreach ($this->netPositions as $vatPercent => $positions) {
            $positionGroups[] = new PositionGroup(PositionGroup::NET, $vatPercent, $positions);
        }
        foreach ($this->grossPositions as $vatPercent => $positions) {
            $positionGroups[] = new PositionGroup(PositionGroup::GROSS, $vatPercent, $positions);
        }

        return new Invoice(...$positionGroups);
    }
}