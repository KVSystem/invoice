<?php

namespace Proengeno\Invoice\Calculator;

use Proengeno\Invoice\Interfaces\Calculator;

final class BcMathCalculator implements Calculator
{
    private $scale;

    public function __construct(int $scale = 14)
    {
        $this->scale = $scale;
    }

    public static function isSupported(): bool
    {
        return extension_loaded('bcmath');
    }

    public function compare($a, $b): int
    {
        return bccomp($a, $b, $this->scale);
    }

    public function add($amount, $addend): float
    {
        return (float) bcadd($amount, $addend, $this->scale);
    }

    public function subtract($amount, $subtrahend): float
    {
        return (float) bcsub($amount, $subtrahend, $this->scale);
    }

    public function multiply($amount, $multiplier): float
    {
        return (float) bcmul($amount, $multiplier, $this->scale);
    }

    public function divide($amount, $divisor): float
    {
        return (float) bcdiv($amount, $divisor, $this->scale);
    }

    public function mod($amount, $divisor): float
    {
        return (float) bcmod($amount, $divisor);
    }
}
