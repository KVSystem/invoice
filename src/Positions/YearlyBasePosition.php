<?php

namespace Proengeno\Invoice\Positions;

use DateTime;
use InvalidArgumentException;
use Proengeno\Invoice\Invoice;

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
     * @psalm-param array{name: string, price: float, from: string, until: string} $attributes
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
            'from' => $this->from()->format('Y-m-d H:i:s'),
            'until' => $this->until()->format('Y-m-d H:i:s'),
        ];
    }
}
