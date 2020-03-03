<?php

namespace Proengeno\Invoice\Formatter;

use DateTime;

class DateIntervalFormatter implements TypeFormatter
{
    private $pattern;

    public function __construct(string $locale)
    {
        if ($locale == 'de_DE') {
            $this->pattern = '%a Tage';
        }
    }

    public function setPattern(string $pattern): void
    {
        $this->pattern = $pattern;
    }

    public function format($value): string
    {
        return $value->format($this->pattern);
    }
}
