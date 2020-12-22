<?php

declare(strict_types=1);

namespace Proengeno\Invoice\Calculator;

use Proengeno\Invoice\Interfaces\Calculator;

final class BcMathCalculator implements Calculator
{
    private int $scale;

    public function __construct(int $scale = 14)
    {
        $this->scale = $scale;
    }

    public static function isSupported(): bool
    {
        return extension_loaded('bcmath');
    }

    public function compare(float $a, float $b): int
    {
        return bccomp($this->formatValue($a), $this->formatValue($b), $this->scale);
    }

    public function add(float $amount, float $addend): float
    {
        return (float) bcadd($this->formatValue($amount), $this->formatValue($addend), $this->scale);
    }

    public function subtract(float $amount, float $subtrahend): float
    {
        return (float) bcsub($this->formatValue($amount), $this->formatValue($subtrahend), $this->scale);
    }

    public function multiply(float $amount, float $multiplier): float
    {
        return (float) bcmul($this->formatValue($amount), $this->formatValue($multiplier), $this->scale);
    }

    public function divide(float $amount, float $divisor): float
    {
        return (float) bcdiv($this->formatValue($amount), $this->formatValue($divisor), $this->scale);
    }

    public function mod(float $amount, float $divisor): float
    {
        return (float) bcmod($this->formatValue($amount), $this->formatValue($divisor));
    }

    /**
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     * @return numeric-string
     */
    private function formatValue(float $value)
    {
        return number_format($value, $this->scale, '.', '');
    }
}
