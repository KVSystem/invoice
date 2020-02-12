<?php

namespace Proengeno\Invoice\Formatter;

use DateTime;

class DateFormatter
{
    private $pattern;

    public function __construct(string $locale)
    {
        if ($locale == 'de_DE') {
            $this->pattern = 'd.m.Y';
        }
    }

    public function setPattern(string $pattern): void
    {
        $this->pattern = $pattern;
    }

    public function format(DateTime $value): string
    {
        return $value->format($this->pattern);
    }
}
