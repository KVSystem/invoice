<?php

namespace Proengeno\Invoice;

use Proengeno\Invoice\Formatter\Formatter;
use Proengeno\Invoice\Interfaces\Position;
use Proengeno\Invoice\Positions\PositionGroup;

class Factory
{
    private ?Formatter $formatter = null;
    private array $netPositions = [];
    private array $grossPositions = [];

    public function addFormatter(Formatter $formatter): self
    {
        $this->formatter = $formatter;

        return $this;
    }

    public function addNetPosition(float $vatPercent, Position $position): self
    {
        /**
         * @psalm-suppress InvalidPropertyAssignmentValue
         * @psalm-suppress InvalidArrayOffset
         */
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
        /**
         * @psalm-suppress InvalidPropertyAssignmentValue
         * @psalm-suppress InvalidArrayOffset
         */
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
            $positionGroups[] = new PositionGroup(PositionGroup::NET, (float) $vatPercent, $positions);
        }
        foreach ($this->grossPositions as $vatPercent => $positions) {
            $positionGroups[] = new PositionGroup(PositionGroup::GROSS, (float) $vatPercent, $positions);
        }

        $invoice = new Invoice(...$positionGroups);

        if (null !== $this->formatter) {
            $invoice->setFormatter($this->formatter);
        }

        return $invoice;
    }
}
