<?php

namespace Proengeno\Invoice\Formatter;

use NumberFormatter;
use Proengeno\Invoice\Interfaces\TypeFormatter;

class IntegerFormatter implements TypeFormatter
{
    protected $formatter;

    public function __construct(string $locale)
    {
        $this->formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
    }

    public function setPattern(string $pattern): void
    {
        $this->formatter->setPattern($pattern);
    }

    public function format($value): string
    {
        return $this->formatter->format($value / 100);
    }
}
