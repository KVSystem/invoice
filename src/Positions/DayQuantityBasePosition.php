<?php

namespace Proengeno\Invoice\Positions;

use DateTime;
use InvalidArgumentException;
use Proengeno\Invoice\Invoice;

use function Proengeno\Invoice\getYearlyFactor;

class DayQuantityBasePosition extends AbstractPeriodPosition
{
    public function __construct(string $name, float $price, float $quantity, DateTime $from, DateTime $until)
    {
        if ($until->format('Ymd') < $from->format('Ymd')) {
            throw new InvalidArgumentException($until->format('Y-m-d') . ' must be greater/equal than ' . $from->format('Y-m-d'));
        }

        if ($until->format('Y') !== $from->format('Y')) {
            throw new InvalidArgumentException('Timerange must be in the same year, ['. $from->format('Y') .' / ' . $until->format('Y') . '] given.');
        }

        $this->name = $name;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->from = $from;
        $this->until = $until;
    }

    /**
     * @param array{name: string, price: float, from: string, until: string, quantity: float} $attributes
     */
    public static function fromArray(array $attributes): self
    {
        return new self(
            $attributes['name'],
            $attributes['price'],
            $attributes['quantity'],
            new DateTime($attributes['from']),
            new DateTime($attributes['until']),
        );
    }

    public function price(): float
    {
        return $this->priceYearlyBased();
    }

    public function priceDayBased(): float
    {
        return $this->price;
    }

    public function priceYearlyBased(): float
    {
        return Invoice::getCalulator()->multiply(
            $this->price, getYearlyFactor($this->from, $this->until)
        );
    }

    public function amount(): float
    {
        return round($this->unroundedAmount(), 2);
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name(),
            'price' => $this->price,
            'amount' => $this->amount(),
            'from' => $this->from()->format('Y-m-d H:i:s'),
            'until' => $this->until()->format('Y-m-d H:i:s'),
            'quantity' => $this->quantity(),
        ];
    }

    private function unroundedAmount(): float
    {
        return Invoice::getCalulator()->multiply(
            Invoice::getCalulator()->multiply($this->quantity, $this->days()),
            $this->price
        );
    }
}
