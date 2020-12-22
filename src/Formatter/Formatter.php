<?php

namespace Proengeno\Invoice\Formatter;

use DateTime;
use TypeError;
use DateInterval;
use ReflectionClass;
use InvalidArgumentException;
use Proengeno\Invoice\Interfaces\Position;
use Proengeno\Invoice\Interfaces\Formatable;
use Proengeno\Invoice\Interfaces\TypeFormatter;

class Formatter
{
    protected array $defaults = [
        'quantity:pattern' => "#,##0.###",
    ];

    protected string $locale;
    protected array $pattern;

    private static string $dateFormatter = DateFormatter::class;
    private static string $floatFormatter = FloatFormatter::class;
    private static string $integerFormatter = IntegerFormatter::class;
    private static string $dateIntervalFormatter = DateIntervalFormatter::class;

    public function __construct(string $locale, array $pattern = [])
    {
        $this->locale = $locale;
        $this->pattern = $pattern;
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
                self::$integerFormatter = $class;
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

        if (null !== $pattern = $this->getPattern($patternName, $method)) {
            $formatter->setPattern($pattern);
        }
        if ($this->hasMulitplier($patternName, $method)) {
            $value *= $this->getMulitplier($patternName, $method);
        }

        return $formatter->format($value);
    }

    /** @param mixed $value */
    protected function newFormatter($value): TypeFormatter
    {
        if (is_int($value)) {
            /** @var IntegerFormatter */
            return new self::$integerFormatter($this->locale);
        }
        if (is_float($value)) {
            /** @var FloatFormatter */
            return new self::$floatFormatter($this->locale);
        }
        if ($value instanceof DateTime) {
            /** @var DateFormatter */
            return new self::$dateFormatter($this->locale);
        }
        if ($value instanceof DateInterval) {
            /** @var DateIntervalFormatter */
            return new self::$dateIntervalFormatter($this->locale);
        }
        throw new InvalidArgumentException("Value $value can't be formated");
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
