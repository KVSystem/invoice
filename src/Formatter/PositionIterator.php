<?php

namespace Proengeno\Invoice\Formatter;

use Proengeno\Invoice\Formatter\Formatter;

class PositionIterator extends \ArrayIterator
{
    private $formatter;

    public function __construct(array $array = [], Formatter $formatter = null, int $flags = 0)
    {
        $this->formatter = $formatter;
        parent::__construct($array, $flags);
    }

    public function current()
    {
        $position = parent::current();
        $position->setFormatter($this->formatter);
        return $position;
    }
}
