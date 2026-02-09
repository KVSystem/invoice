<?php

declare(strict_types=1);

namespace Proengeno\Invoice;

use DateTimeImmutable;
use DateTimeInterface;

function getYearlyFactor(DateTimeInterface $from, DateTimeInterface $until): float
{
    $current = DateTimeImmutable::createFromInterface($from);
    $end = DateTimeImmutable::createFromInterface($until);

    $totalDays = 0;
    $weightedSum = 0.0;

    while ($current < $end) {
        $year = (int) $current->format('Y');
        $yearEnd = new DateTimeImmutable(($year + 1) . '-01-01');
        $segmentEnd = $yearEnd < $end ? $yearEnd : $end;

        $daysInSegment = (int) $current->diff($segmentEnd)->days;
        $daysInYear = ((int) $current->format('L')) === 1 ? 366 : 365;

        $totalDays += $daysInSegment;
        $weightedSum += $daysInSegment * $daysInYear;

        $current = $segmentEnd;
    }

    if ($totalDays === 0) {
        return 365.0;
    }

    return Invoice::getCalulator()->divide($weightedSum, $totalDays);
}
