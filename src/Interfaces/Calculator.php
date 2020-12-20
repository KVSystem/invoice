<?php

namespace Proengeno\Invoice\Interfaces;

use Proengeno\Invoice\Formatter\Formatter;

interface Calculator
{
    public static function isSupported(): bool;

    public function compare(float $a, float $b): int;

    public function add(float $amount, float $addend): float;

    public function subtract(float $amount, float $subtrahend): float;

    public function multiply(float $amount, float $multiplier): float;

    public function divide(float $amount, float $divisor): float;

    public function mod(float $amount, float $divisor): float;
}
