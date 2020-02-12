<?php

namespace Proengeno\Invoice\Positions;

use DateTime;
use Proengeno\Invoice\Formatter\FormatableTrait;

class PeriodPosition implements PeriodPositionInterface
{
    use FormatableTrait;

    private $from;
    private $until;
    private $formatter;
    private $corePosition;

    public function __construct(DateTime $from, DateTime $until, Position $corePosition)
    {
        $this->from = $from;
        $this->until = $until;
        $this->corePosition = $corePosition;
    }

    public function name(): string
    {
        return $this->corePosition->name();
    }

    public function from(): DateTime
    {
        return $this->from;
    }

    public function until(): DateTime
    {
        return $this->until;
    }

    public function price(): float
    {
        return $this->corePosition->price();
    }

    public function quantity(): float
    {
        return $this->corePosition->quantity();
    }

    public function amount(): int
    {
        return $this->corePosition->amount();
    }

    public function jsonSerialize()
    {
        return array_merge($this->corePosition->jsonSerialize(), [
            'from' => $this->from()->format('Y-m-d'),
            'until' => $this->until()->format('Y-m-d'),
        ]);
    }
}
