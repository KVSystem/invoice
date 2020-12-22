<?php

namespace Proengeno\Invoice\Positions;

use DateTime;
use Proengeno\Invoice\Formatter\FormatableTrait;

class DatePosition extends AbstractPosition
{
    private DateTime $date;

    public function __construct(string $name, float $price, float $quantity, DateTime $date)
    {
        $this->name = $name;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->date = $date;
    }

    public static function fromArray(array $attributes): self
    {
        extract($attributes);

        return new self($name, $price, $quantity, new DateTime($date));
    }

    public function date(): DateTime
    {
        return $this->date;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name(),
            'price' => $this->price(),
            'amount' => $this->amount(),
            'quantity' => $this->quantity(),
            'date' => $this->date()->format('Y-m-d'),
        ];
    }
}
