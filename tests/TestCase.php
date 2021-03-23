<?php

namespace Proengeno\Invoice\Test;

use Proengeno\Invoice\Test\Fakes\ModelFake;
use Proengeno\Invoice\Test\Fakes\PositionsFake;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function workingPriceFake($name, $price, $decimal, $effectivFrom, $effectiveUntil)
    {
        return $this->positionFake($name, $price, $decimal, $effectivFrom, $effectiveUntil, 'working_price');
    }

    protected function basicPriceFake($name, $price, $decimal, $effectivFrom, $effectiveUntil)
    {
        return $this->positionFake($name, $price, $decimal, $effectivFrom, $effectiveUntil, 'basic_price');
    }

    protected function positionFake($name, $price, $decimal, $effectivFrom, $effectiveUntil, $priceType)
    {
        return new PositionsFake([
            'name' => $name,
            $priceType => $price,
            'decimal' => $decimal,
            'effective_from' => new \DateTime($effectivFrom),
            'effective_until' => new \DateTime($effectiveUntil)
        ]);
    }

    protected function metercountFake($meterNo, $type, $ableseDatum, $reading)
    {
        return new ModelFake([
            'ablese_datum' => new \DateTime($ableseDatum),
            'zaehlerstand_1' => $reading,
            'reading_type' => $type,
            'meter' => (object)['zaehler_nr' => $meterNo]
        ]);
    }
}
