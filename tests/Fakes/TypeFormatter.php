<?php

namespace Proengeno\Invoice\Test\Fakes;

use Proengeno\Invoice\Interfaces\TypeFormatter as FormatterInterface;

class TypeFormatter implements FormatterInterface
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
