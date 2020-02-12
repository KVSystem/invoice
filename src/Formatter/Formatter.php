<?php

namespace Proengeno\Invoice\Formatter;

use Proengeno\Invoice\Formatter\DateFormatter;
use Proengeno\Invoice\Formatter\FloatFormatter;
use Proengeno\Invoice\Formatter\IntegerFormatter;
use Proengeno\Invoice\Positions\PositionInterface;

class Formatter
{
    protected $locale;
    protected $pattern;
    protected $mulitplier;

    private static $dateFormatter = DateFormatter::class;
    private static $floatFormatter = FloatFormatter::class;
    private static $intergerFormatter = IntegerFormatter::class;

    public static function setDateFormatter($class)
    {
        self::$dateFormatter = $class;
    }

    public static function setFloatFormatter($class)
    {
        self::$floatFormatter = $class;
    }

    public static function setIntergerFormatter($class)
    {
        self::$intergerFormatter = $class;
    }

    public function __construct(string $locale, array $pattern = [])
    {
        $this->locale = $locale;
        $this->pattern = $pattern;
    }

    public function format(Formatable $formatable, $method)
    {
        $formatter = $this->newFormatter($value = $formatable->$method());

        if (!$formatable instanceof PositionInterface) {
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
}
