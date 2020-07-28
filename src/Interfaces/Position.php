<?php

namespace Proengeno\Invoice\Interfaces;

interface Position extends \JsonSerializable, Formatable
{
    public static function fromArray(array $attributes);

    public function name(): string;

    public function quantity(): float;

    // Can be a fraction of the smallest currency unit
    public function price(): float;

    // Representas the smallest currency unit and must therfor be a an interger
    public function amount(): int;

    public function format(string $method): string;

    public function jsonSerialize(): array;
}
