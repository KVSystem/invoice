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
        return bccomp($this->formatValue($a), $this->formatValue($b), $this->scale);
    }

    public function add($amount, $addend): float
    {
        return (float) bcadd($this->formatValue($amount), $this->formatValue($addend), $this->scale);
    }

    public function subtract($amount, $subtrahend): float
    {
        return (float) bcsub($this->formatValue($amount), $this->formatValue($subtrahend), $this->scale);
    }

    public function multiply($amount, $multiplier): float
    {
        return (float) bcmul($this->formatValue($amount), $this->formatValue($multiplier), $this->scale);
    }

    public function divide($amount, $divisor): float
    {
        return (float) bcdiv($this->formatValue($amount), $this->formatValue($divisor), $this->scale);
    }

    public function mod($amount, $divisor): float
    {
        return (float) bcmod($this->formatValue($amount), $this->formatValue($divisor));
    }

    private function formatValue($value)
    {
        return number_format($value, $this->scale, '.', '');
    }
}
