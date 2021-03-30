<?php

declare(strict_types=1);

namespace Proengeno\Invoice\Formatter;

use DateTimeInterface;

final class DateFormatter
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

    public function format(DateTimeInterface $value): string
    {
        return $value->format($this->pattern);
    }
}
