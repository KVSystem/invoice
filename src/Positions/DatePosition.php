<?php

namespace Proengeno\Invoice\Positions;

use DateTime;
use Proengeno\Invoice\Formatter\FormatableTrait;

class DatePosition extends Position
{
    use FormatableTrait;

    private $date;

    public function __construct(string $name, float $price, float $quantity, DateTime $date)
    {
        parent::__construct($name, $price, $quantity);
        $this->date = $date;
    }

    public static function fromArray(array $attributes)
    {
        extract($attributes);

        return new static($name, $price, $quantity, new DateTime($date));
    }

    public function date(): DateTime
    {
        return $this->date;
    }

    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'date' => $this->date()->format('Y-m-d'),
        ]);
    }
}
