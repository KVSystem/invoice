<?php

namespace Proengeno\Invoice\Positions;

use DateTime;
use InvalidArgumentException;
use Proengeno\Invoice\Invoice;

class YearlyQuantityBasePosition extends PeriodPosition
{
    private float $publicQuantity;

    public function __construct(string $name, float $price, float $quantity, DateTime $from, DateTime $until)
    {
        if ($until->format('Ymd') < $from->format('Ymd')) {
            throw new InvalidArgumentException($until->format('Y-m-d') . ' must be greater/equal than ' . $from->format('Y-m-d'));
        }
        $this->name = $name;
        $this->quantity = self::calculateQuantity($from, $until, $quantity);
        $this->price = $price;
        $this->from = $from;
        $this->until = $until;
        $this->publicQuantity = $quantity;
    }

    /**
     * @psalm-param array{name: string, price: float, quantity: float, from: string, until: string} $attributes
     */
    public static function fromArray(array $attributes): self
    {
        return new self(
            $attributes['name'],
            $attributes['price'],
            $attributes['quantity'],
            new DateTime($attributes['from']),
            new DateTime($attributes['until'])
        );
    }

    private static function calculateQuantity(DateTime $from, DateTime $until, float $quantity): float
    {
        return round(Invoice::getCalulator()->multiply(
            Invoice::getCalulator()->multiply(
                self::getYearlyFactor($from, $until), $until->diff($from)->days + 1
            ),
            $quantity
        ), 13);
    }

    public function quantity(): float
    {
        return $this->publicQuantity;
    }

    public function amount(): float
    {
        return round(
            Invoice::getCalulator()->multiply($this->price(), $this->quantity), 2
        );
    }

    public function yearlyAmount(): float
    {
        return Invoice::getCalulator()->multiply(
            $this->amount(), $this->until()->format('L') ? 366 : 365
        );
    }

    private static function getYearlyFactor(DateTime $from, DateTime $until): float
    {
        $current = clone $from;

        $leapDays = 0;
        $leapDevider = 1;
        $leapAddition = 0;
        while ($current->modify("last day of feb") && $current->format('Ymd') <= $until->format('Ymd')) {
            if ($current->format('d') == 29 && $current->format('Ymd') >= $from->format('Ymd')) {
                $leapDays++;
            }
            if ($current->format('d') == 28 && $current->format('Ymd') >= $from->format('Ymd')) {
                $leapDevider++;
            }
            $current->modify("+1 year");
        }

        if ($leapDays > 0) {
            $leapAddition = Invoice::getCalulator()->divide($leapDays, $leapDevider);
        }

        return Invoice::getCalulator()->divide(
            1, Invoice::getCalulator()->add(365, $leapAddition)
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name(),
            'price' => $this->price(),
            'amount' => $this->amount(),
            'quantity' => $this->quantity(),
            'from' => $this->from()->format('Y-m-d H:i:s'),
            'until' => $this->until()->format('Y-m-d H:i:s'),
        ];
    }
}
