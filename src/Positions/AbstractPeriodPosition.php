<?php

namespace Proengeno\Invoice\Positions;

use DateInterval;
use DateTime;
use Proengeno\Invoice\Interfaces\PeriodPosition as PeriodPositionInterface;

abstract class AbstractPeriodPosition extends AbstractPosition implements PeriodPositionInterface
{
    protected DateTime $from;
    protected DateTime $until;

    public function from(): DateTime
    {
        return $this->from;
    }

    public function until(): DateTime
    {
        return $this->until;
    }

    public function period(): DateInterval
    {
        return (clone $this->from())->modify('-1 day')->diff($this->until());
    }

    public function days(): int
    {
        return $this->period()->days;
    }
}
