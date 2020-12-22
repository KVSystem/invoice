<?php

declare(strict_types=1);

namespace Proengeno\Invoice\Formatter;

use Proengeno\Invoice\Formatter\Formatter;
use Proengeno\Invoice\Interfaces\Position;

class PositionIterator extends \ArrayIterator
{
    private ?Formatter $formatter;

    public function __construct(array $array = [], Formatter $formatter = null, int $flags = 0)
    {
        $this->formatter = $formatter;
        parent::__construct($array, $flags);
    }

    public function current(): Position
    {
        $position = parent::current();
        $position->setFormatter($this->formatter);
        return $position;
    }
}
