<?php

namespace Proengeno\Invoice\Test\Calculator;

use Proengeno\Invoice\Calculator\BcMathCalculator;
use Proengeno\Invoice\Test\TestCase;

class McMathCalculatorTest extends TestCase
{
    /** @test */
    public function it_can_add_two_values_even_with_large_floats()
    {
        $largeFloats[] = 0.000001;
        $largeFloats[] = 0.000000000001;
        $largeFloats[] = 0.001000001001;

        foreach ($largeFloats as $largeFloat) {
            $calculator = new BcMathCalculator;

            $result = $calculator->add($largeFloat, 1);

            $this->assertSame($largeFloat + 1, $result);
        }
    }
}
