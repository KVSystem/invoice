<?php

declare(strict_types=1);

namespace Proengeno\Invoice\Formatter;

use DateInterval;

class DateIntervalFormatter
{
    private string $pattern;

    public function __construct(string $locale)
    {
        if ($locale == 'de_DE') {
            $this->pattern = '%a Tage';
        } else {
            $this->pattern = '%a days';
        }
    }

    public function setPattern(string $pattern): void
    {
        $this->pattern = $pattern;
    }

    public function format(DateInterval $value): string
    {
        return $value->format($this->pattern);
    }
}
