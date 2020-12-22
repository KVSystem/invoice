<?php

namespace Proengeno\Invoice\Positions;

use DateTime;
use InvalidArgumentException;
use Proengeno\Invoice\Invoice;

class MonthlyBasePosition extends AbstractPeriodPosition
{
    public function __construct(string $name, float $price, DateTime $from, DateTime $until)
    {
        if ($until < $from) {
            throw new InvalidArgumentException($until->format('Y-m-d H:i:s') . ' must be greaten than ' . $from->format('Y-m-d H:i:s'));
        }
        $this->name = $name;
        $this->quantity = self::calculateQuantity($from, $until);
        $this->price = $price;
        $this->from = $from;
        $this->until = $until;
    }

    private static function calculateQuantity(DateTime $from, DateTime $until): float
    {
        self::getYearlyFactor($from, $until);
        if ($until > $from) {
            return round(
                Invoice::getCalulator()->multiply(self::getYearlyFactor($from, $until), $until->diff($from)->days + 1), 6
            );
        }
        throw new InvalidArgumentException($until->format('Y-m-d H:i:s') . ' must be greaten than ' . $from->format('Y-m-d H:i:s'));
    }

    public static function fromArray(array $attributes): self
    {
        extract($attributes);

        return new self($name, $price, new DateTime($from), new DateTime($until));
    }

    public function yearlyAmount(): int
    {
        return (int) round(Invoice::getCalulator()->multiply(
            Invoice::getCalulator()->divide($this->amount(), $this->quantity()), 12
        ), 0);
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
            12, Invoice::getCalulator()->add(365, $leapAddition)
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
