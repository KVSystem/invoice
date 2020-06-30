<?php

namespace Proengeno\Invoice\Interfaces;

use Proengeno\Invoice\Formatter\Formatter;

interface Calculator
{
    public static function isSupported(): bool;

    public function compare($a, $b): int;

    public function add($amount, $addend): float;

    public function subtract($amount, $subtrahend): float;

    public function multiply($amount, $multiplier);

    public function divide($amount, $divisor): float;

    public function mod($amount, $divisor): float;
}
