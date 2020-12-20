<?php

namespace Proengeno\Invoice\Formatter;

use DateTime;
use Proengeno\Invoice\Interfaces\TypeFormatter;

class DateFormatter implements TypeFormatter
{
    private string $pattern;

    public function __construct(string $locale)
    {
        if ($locale == 'de_DE') {
            $this->pattern = 'd.m.Y';
        } else {
            $this->pattern = 'd.m.Y';
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
