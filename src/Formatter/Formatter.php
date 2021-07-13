<?php

declare(strict_types=1);

namespace Proengeno\Invoice\Formatter;

use DateInterval;
use DateTimeInterface;
use Proengeno\Invoice\Interfaces\Position;
use Proengeno\Invoice\Interfaces\Formatable;
use TypeError;

class Formatter
{
    /** @var array<string, string> */
    protected array $defaults = [
        'quantity:pattern' => "#,##0.###",
    ];

    protected string $locale;

    /** @var array<string, array<string, string|int>> */
    protected array $pattern;

    /** @param array<string, array<string, string|int>> $pattern */
    public function __construct(string $locale, array $pattern = [])
    {
        $this->locale = $locale;
        $this->pattern = $pattern;
    }

    public function format(Formatable $formatable, string $method, array $attributes = []): string
    {
        /** @var float|DateTimeInterface|DateInterval */
        $value = $formatable->$method(...$attributes);

        $formatter = $this->newFormatter($value);

        if ($formatable instanceof Position) {
            $patternName = $formatable->name();
        } else {
            $patternName = get_class($formatable);
        }

        if (($pattern = $this->getPattern($patternName, $method)) && is_string($pattern)) {
            $formatter->setPattern($pattern);
        }
        if (is_float($value) && $this->hasMulitplier($patternName, $method)) {
            $value *= $this->getMulitplier($patternName, $method);
        }

        /** @psalm-suppress InvalidArgument */
        return $formatter->format($value);
    }

    protected function newFormatter(float|DateTimeInterface|DateInterval $value): DateFormatter|FloatFormatter|DateIntervalFormatter
    {
        if ($value instanceof DateTimeInterface) {
            return new DateFormatter($this->locale);
        }
        if ($value instanceof DateInterval) {
            return new DateIntervalFormatter($this->locale);
        }
        return new FloatFormatter($this->locale);
    }

    protected function getPattern(string $name, string $method): string|int|null
    {
        if (isset($this->pattern[$name]["$method:pattern"])) {
            return $this->pattern[$name]["$method:pattern"];
        }
        if (isset($this->defaults["$method:pattern"])) {
            return $this->defaults["$method:pattern"];
        }
        return null;
    }

    protected function hasMulitplier(string $name, string $method): bool
    {
        return $this->getMulitplier($name, $method) !== 1;
    }

    protected function getMulitplier(string $name, string $method): int
    {
        $multiplier = $this->pattern[$name]["$method:multiplier"] ?? 1;

        if (is_int($multiplier)) {
            return $multiplier;
        }

        throw new TypeError("Multiplier for $method:multiplier must be an interger got $multiplier.");
    }
}
