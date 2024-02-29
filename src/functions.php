<?php

declare(strict_types=1);

namespace Proengeno\Invoice;

use DateTimeInterface;

function getYearlyFactor(DateTimeInterface $from, DateTimeInterface $until): float
{
    $currentYear = (int)$from->format('Y');
    $untilYear = (int)$until->format('Y');

    $leapDays = 0.0;
    $leapDevider = 0;
    do {
        $leapDevider++;
        if ($from->format('L') === '1') {
            $leapDays += 1;
            $leapDays /= $leapDevider;
        }

        $currentYear++;
    } while ($currentYear <= $untilYear);

    return Invoice::getCalulator()->add(365, $leapDays);
}
