<?php

namespace Proengeno\Invoice\Test\Fakes;

class TypeFormatter
{
    public function __construct(string $locale)
    {
        //
    }

    public function setPattern(string $pattern): void
    {

    }

    public function format($value): string
    {
        if ($value instanceof \DateTime) {
            return 'FAKE:'.$value->format('Y-m-d');
        }
        if ($value instanceof \DateInterval) {
            return 'FAKE:'.$value->format('%a');
        }
        return 'FAKE:'.$value;
    }
}
