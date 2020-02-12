<?php

namespace Proengeno\Invoice\Positions;

use Money\Money;
use Proengeno\Invoice\Formatter\Formatter;
use Proengeno\Invoice\Formatter\Formatable;

interface PositionInterface extends \JsonSerializable, Formatable
{
    public function name(): string;
    public function quantity(): float;
    public function price(): float;
    public function amount(): int;
    public function format(string $method): string;
    public function jsonSerialize();
}
