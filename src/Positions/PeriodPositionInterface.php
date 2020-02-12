<?php

namespace Proengeno\Invoice\Positions;

use DateTime;

interface PeriodPositionInterface extends PositionInterface
{
    public function from(): DateTime;
    public function until(): DateTime;
}
