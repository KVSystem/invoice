<?php

namespace Proengeno\Invoice\Interfaces;

use Proengeno\Invoice\Formatter\Formatter;

interface TypeFormatter
{
    public function __construct(string $locale);

    public function setPattern(string $pattern): void;

    /** @param mixed $value */
    public function format($value): string;
}
