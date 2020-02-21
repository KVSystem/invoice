<?php

namespace Proengeno\Invoice\Formatter;

use Proengeno\Invoice\Positions\PositionInterface;

class Formatter
{
    protected $locale;
    protected $pattern;
    protected $mulitplier;

    private static $dateFormatter;
    private static $floatFormatter;
    private static $intergerFormatter;

    public function __construct(string $locale, array $pattern = [])
    {
        $this->locale = $locale;
        $this->pattern = $pattern;

        if (null === self::$dateFormatter) {
            self::setDateFormatter(DateFormatter::class);
        }
        if (null === self::$floatFormatter) {
            self::setFloatFormatter(FloatFormatter::class);
        }
        if (null === self::$intergerFormatter) {
            self::setIntergerFormatter(IntegerFormatter::class);
        }
    }

    public static function setDateFormatter(string $class)
    {
        if (false === isset(class_implements($class)[TypeFormatter::class])) {
            throw self::missignInterfaceError(TypeFormatter::class, 1, __FUNCTION__, __LINE__);
        }
        self::$dateFormatter = $class;
    }

    public static function setFloatFormatter($class)
    {
        if (false === isset(class_implements($class)[TypeFormatter::class])) {
            throw self::missignInterfaceError(TypeFormatter::class, 1, __FUNCTION__, __LINE__);
        }
        self::$floatFormatter = $class;
    }

    public static function setIntergerFormatter($class)
    {
        if (false === isset(class_implements($class)[TypeFormatter::class])) {
            throw self::missignInterfaceError(TypeFormatter::class, 1, __FUNCTION__, __LINE__);
        }
        self::$intergerFormatter = $class;
    }

    public function format(Formatable $formatable, $method)
    {
        $formatter = $this->newFormatter($value = $formatable->$method());

        if (false === $formatable instanceof PositionInterface) {
            return $formatter->format($value);
        }
        if ($this->hasPattern($formatable, $method)) {
            $formatter->setPattern($this->getPattern($formatable, $method));
        }
        if ($this->hasMulitplier($formatable, $method)) {
            return $formatter->format($value * $this->getMulitplier($formatable, $method));
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
    }

    protected function hasPattern($formatable, $method)
    {
        return !! $this->getPattern($formatable, $method);
    }

    protected function getPattern($formatable, $method)
    {
        return $this->pattern[$formatable->name()]["$method:pattern"] ?? null;
    }

    protected function hasMulitplier($formatable, $method)
    {
        return $this->getMulitplier($formatable, $method) !== 1;
    }

    protected function getMulitplier($formatable, $method)
    {
        return $this->pattern[$formatable->name()]["$method:multiplier"] ?? 1;
    }

    private static function missignInterfaceError($interface, $argumentCount, $function, $line)
    {
        return new \TypeError(
            "Argument $argumentCount passed to " . __CLASS__ . "::$function() must implement interface $interface, called in " . __FILE__ . " on line $line"
        );
    }
}
