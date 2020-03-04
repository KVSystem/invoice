<?php

namespace Proengeno\Invoice\Interfaces;

use DateTime;

interface PeriodPosition extends Position
{
    public function from(): DateTime;
    public function until(): DateTime;
}
