<?php

namespace Proengeno\Invoice\Interfaces;

use DateTime;
use DateInterval;

interface PeriodPosition extends Position
{
    public function from(): DateTime;
    public function until(): DateTime;
    public function period(): DateInterval;
}
