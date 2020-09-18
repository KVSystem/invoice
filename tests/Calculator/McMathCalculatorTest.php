<?php

namespace Proengeno\Invoice\Test\Calculator;

use Proengeno\Invoice\Calculator\BcMathCalculator;
use Proengeno\Invoice\Test\TestCase;

class McMathCalculatorTest extends TestCase
{
    /** @test */
    public function it_can_add_two_values_even_with_large_floats()
    {
        $largeFloat = 0.00001;
        $calculator = new BcMathCalculator;

        $result = $calculator->add(0.00001, 1);

        $this->assertSame($largeFloat + 1, $result);
    }
}
