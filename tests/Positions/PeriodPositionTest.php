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
    public function it_always_provides_the_pruduct_price_as_commercial_roundet_int()
    {
        $position = new PeriodPosition(new DateTime, new DateTime, new Position('test', 12.12, 100.0));

        $this->assertEquals(121200, $position->amount());
    }

    /** @test **/
    public function it_can_build_from_an_array()
    {
        $oldPosition = new PeriodPosition(new DateTime(date('Y-m-d')), new DateTime(date('Y-m-d')), new Position('Test1', 2.555, 1));
        $newPosition = PeriodPosition::fromArray($oldPosition->jsonSerialize());

        $this->assertEquals($oldPosition->from(), $newPosition->from());
        $this->assertEquals($oldPosition->name(), $newPosition->name());
        $this->assertEquals($oldPosition->until(), $newPosition->until());
        $this->assertEquals($oldPosition->price(), $newPosition->price());
        $this->assertEquals($oldPosition->amount(), $newPosition->amount());
        $this->assertEquals($oldPosition->quantity(), $newPosition->quantity());
    }
}
