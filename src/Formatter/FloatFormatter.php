<?php

declare(strict_types=1);

namespace Proengeno\Invoice\Formatter;

use NumberFormatter;

final class FloatFormatter
{
    protected NumberFormatter $formatter;

    public function __construct(string $locale)
    {
        $this->formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
    }

    public function setPattern(string $pattern): void
    {
        $this->formatter->setPattern($pattern);
    }

    public function format(float $value): string
    {
        return $this->formatter->format($value);
    }
}
