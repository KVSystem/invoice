<?php

namespace Proengeno\Invoice\Test;

use DateTime;
use Money\Money;
use Proengeno\Invoice\Test\TestCase;
use Proengeno\Invoice\Positions\Position;
use Proengeno\Invoice\Positions\PeriodPosition;

class PeriodPositionTest extends TestCase
{
    /** @test **/
    public function it_provides_the_given_border_dates()
    {
        $from  = new DateTime;
        $until = (new DateTime)->modify('+1 year');
        $position = new PeriodPosition($from, $until, new Position('test', 1, 1));

        $this->assertEquals($from, $position->from());
        $this->assertEquals($until, $position->until());
    }

    /** @test **/
    public function it_provides_the_position_name()
    {
        $position = new PeriodPosition(new DateTime, new DateTime, new Position('test', 1, 1));

        $this->assertEquals('test', $position->name());
    }

    /** @test **/
    public function it_provides_the_given_quantity_price()
    {
        $position = new PeriodPosition(new DateTime, new DateTime, new Position('test', 1.22, 1.0));

        $this->assertEquals(1.22, $position->price());
    }

    /** @test **/
    public function it_provides_the_given_quantity()
    {
        $position = new PeriodPosition(new DateTime, new DateTime, new Position('test', 1.0, 1.55));

        $this->assertEquals(1.55, $position->quantity());
    }

    /** @test **/
    public function it_always_provides_the_pruduct_price_as_money_instance()
    {
        $position = new PeriodPosition(new DateTime, new DateTime, new Position('test', 12.12, 100.0));

        $this->assertEquals(121200, $position->amount());
    }
}
