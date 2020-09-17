<?php

namespace Proengeno\Invoice\Formatter;

use TypeError;
use ReflectionClass;
use InvalidArgumentException;
use Proengeno\Invoice\Interfaces\Position;
use Proengeno\Invoice\Interfaces\Formatable;
use Proengeno\Invoice\Interfaces\TypeFormatter;

class Formatter
{
    protected $defaults = [
        'quantity:pattern' => "#,##0.###",
    ];

    protected $locale;
    protected $pattern;
    protected $mulitplier;

    private static $dateFormatter;
    private static $floatFormatter;
    private static $intergerFormatter;
    private static $dateIntervalFormatter;

    public function __construct(string $locale, array $pattern = [])
    {
        $this->locale = $locale;
        $this->pattern = $pattern;

        if (null === self::$dateFormatter) {
            self::setDateFormatter(DateFormatter::class);
        }
        if (null === self::$dateIntervalFormatter) {
            self::setDateIntervalFormatter(DateIntervalFormatter::class);
        }
        if (null === self::$floatFormatter) {
            self::setFloatFormatter(FloatFormatter::class);
        }
        if (null === self::$intergerFormatter) {
            self::setIntergerFormatter(IntegerFormatter::class);
        }
    }

    public static function setDateFormatter(string $class): void
    {
        if (class_exists($class)) {
            if ((new ReflectionClass($class))->implementsInterface(TypeFormatter::class) ) {
                self::$dateFormatter = $class;
                return;
            }
        }
        throw self::missignInterfaceError(TypeFormatter::class, 1, __FUNCTION__, __LINE__);
    }

    public static function setDateIntervalFormatter(string $class): void
    {
        if (class_exists($class)) {
            if ((new ReflectionClass($class))->implementsInterface(TypeFormatter::class) ) {
                self::$dateIntervalFormatter = $class;
                return;
            }
        }
        throw self::missignInterfaceError(TypeFormatter::class, 1, __FUNCTION__, __LINE__);
    }

    public static function setFloatFormatter(string $class): void
    {
        if (class_exists($class)) {
            if ((new ReflectionClass($class))->implementsInterface(TypeFormatter::class) ) {
                self::$floatFormatter = $class;
                return;
            }
        }
        throw self::missignInterfaceError(TypeFormatter::class, 1, __FUNCTION__, __LINE__);
    }

    public static function setIntergerFormatter(string $class): void
    {
        if (class_exists($class)) {
            if ((new ReflectionClass($class))->implementsInterface(TypeFormatter::class) ) {
                self::$intergerFormatter = $class;
                return;
            }
        }
        throw self::missignInterfaceError(TypeFormatter::class, 1, __FUNCTION__, __LINE__);
    }

    public function format(Formatable $formatable, string $method, array $attributes = []): string
    {
        $formatter = $this->newFormatter($value = $formatable->$method(...$attributes));

        if (false === $formatable instanceof Position) {
            $patternName = get_class($formatable);
        } else {
            $patternName = $formatable->name();
        }

        if ($this->hasPattern($patternName, $method)) {
            $formatter->setPattern($this->getPattern($patternName, $method));
        }
        if ($this->hasMulitplier($patternName, $method)) {
            $value *= $this->getMulitplier($patternName, $method);
        }

        return $formatter->format($value);
    }

    protected function newFormatter($value)
    {
        if (is_int($value)) {
            return new self::$intergerFormatter($this->locale);
        }
        if (is_float($value)) {
            return new self::$floatFormatter($this->locale);
        }
        if ($value instanceof \DateTime) {
            return new self::$dateFormatter($this->locale);
        }
        if ($value instanceof \DateInterval) {
            return new self::$dateIntervalFormatter($this->locale);
        }
        throw new InvalidArgumentException("Value $value can't be formated");
    }

    protected function hasPattern(string $name, string $method): bool
    {
        return !! $this->getPattern($name, $method);
    }

    protected function getPattern(string $name, string $method): ?string
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
        return $this->pattern[$name]["$method:multiplier"] ?? 1;
    }

    private static function missignInterfaceError(string $interface, int $argumentCount, string $function, int $line): TypeError
    {
        return new TypeError(
            "Argument $argumentCount passed to " . __CLASS__ . "::$function() must implement interface $interface, called in " . __FILE__ . " on line $line"
        );
    }
}
