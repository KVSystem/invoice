<?php

namespace Proengeno\Invoice\Positions;

use DateTime;
use InvalidArgumentException;
use Proengeno\Invoice\Invoice;

class YearlyQuantityBasePosition extends PeriodPosition
{
    public function __construct(string $name, float $price, float $quantity, DateTime $from, DateTime $until)
    {
        if ($until->format('Ymd') < $from->format('Ymd')) {
            throw new InvalidArgumentException($until->format('Y-m-d') . ' must be greater/equal than ' . $from->format('Y-m-d'));
        }
        $this->name = $name;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->from = $from;
        $this->until = $until;
    }

    /**
     * @param array{name: string, price: float, quantity: float, from: string, until: string} $attributes
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

    public function amount(): float
    {
        // $amount = $price * $quantity / 365 * $period
        $amount = Invoice::getCalulator()->multiply(
            Invoice::getCalulator()->divide(
                Invoice::getCalulator()->multiply($this->price(), $this->quantity),
                self::getYearlyFactor($this->from, $this->until)
            ),
            $this->from->diff($this->until)->days + 1
        );

        return round($amount, 2);
    }

    public function yearlyAmount(): float
    {
        return Invoice::getCalulator()->multiply(
            $this->amount(), self::getYearlyFactor($this->from, $this->until)
        );
    }

    public function price(): float
    {
        return $this->priceYearlyBased();
    }

    public function priceDayBased(): float
    {
        return Invoice::getCalulator()->divide(
            $this->price, self::getYearlyFactor($this->from, $this->until)
        );
    }

    public function priceYearlyBased(): float
    {
        return $this->price;
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

        return Invoice::getCalulator()->add(365, $leapAddition);
    }
}
