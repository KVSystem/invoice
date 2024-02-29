<?php

namespace Proengeno\Invoice\Positions;

use DateTime;
use InvalidArgumentException;
use Proengeno\Invoice\Invoice;

use function Proengeno\Invoice\getYearlyFactor;

class YearlyBasePosition extends AbstractPeriodPosition
{
    public function __construct(string $name, float $price, DateTime $from, DateTime $until)
    {
        if ($until->format('Ymd') < $from->format('Ymd')) {
            throw new InvalidArgumentException($until->format('Y-m-d') . ' must be greater/equal than ' . $from->format('Y-m-d'));
        }
        $this->name = $name;
        $this->quantity = self::calculateQuantity($from, $until);
        $this->price = $price;
        $this->from = $from;
        $this->until = $until;
    }

    /**
     * @param array{name: string, price: float, from: string, until: string} $attributes
     */
    public static function fromArray(array $attributes): self
    {
        return new self($attributes['name'], $attributes['price'], new DateTime($attributes['from']), new DateTime($attributes['until']));
    }

    private static function calculateQuantity(DateTime $from, DateTime $until): float
    {
        return round(Invoice::getCalulator()->multiply(
            self::getYearlyFactor($from, $until), $until->diff($from)->days + 1
        ), 13);
    }

    private static function getYearlyFactor(DateTime $from, DateTime $until): float
    {
        return Invoice::getCalulator()->divide(1, getYearlyFactor($from, $until));
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name(),
            'price' => $this->price(),
            'amount' => $this->amount(),
            'from' => $this->from()->format('Y-m-d H:i:s'),
            'until' => $this->until()->format('Y-m-d H:i:s'),
        ];
    }
}
