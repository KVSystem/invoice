<?php

namespace Proengeno\Invoice\Interfaces;

interface Position extends \JsonSerializable, Formatable
{
    public static function fromArray(array $attributes);
    public function name(): string;
    public function quantity(): float;
    public function price(): float;
    public function amount(): int;
    public function format(string $method): string;
    public function jsonSerialize(): array;
}
