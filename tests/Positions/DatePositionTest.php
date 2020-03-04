<?php

namespace Proengeno\Invoice\Test;

use DateTime;
use Proengeno\Invoice\Test\TestCase;
use Proengeno\Invoice\Positions\Position;
use Proengeno\Invoice\Formatter\Formatter;
use Proengeno\Invoice\Positions\DatePosition;

class DatePositionTest extends TestCase
{
    /** @test **/
    public function it_provides_the_given_date()
    {
        $position = new DatePosition($date = new DateTime, new Position('test', 1, 1));

        $this->assertEquals($date, $position->date());
    }

    /** @test **/
    public function it_provides_the_position_name()
    {
        $position = new DatePosition($date = new DateTime, new Position('test', 1, 1));

        $this->assertEquals('test', $position->name());
    }

    /** @test **/
    public function it_provides_the_given_quantity_price()
    {
        $position = new DatePosition($date = new DateTime, new Position('test', 1.22, 1));

        $this->assertEquals(1.22, $position->price());
    }

    /** @test **/
    public function it_provides_the_given_quantity()
    {
        $position = new DatePosition($date = new DateTime, new Position('test', 1, 1.55));

        $this->assertEquals(1.55, $position->quantity());
    }

    /** @test **/
    public function it_computes_the_prucuct_of_the_quantity_an_the_price()
    {
        $position = new DatePosition($date = new DateTime, new Position('test', 12, 100));

        $this->assertEquals(12 * 100 * 100, $position->amount());
    }

    /** @test **/
    public function it_roundes_the_amount_price_on_two_decimals()
    {
        $position = new DatePosition($date = new DateTime, new Position('Test1', 2.555, 1));
        $this->assertEquals(256, $position->amount());
    }

    /** @test **/
    public function it_can_build_from_an_array()
    {
        $oldPosition = new DatePosition($date = new DateTime(date('Y-m-d')), new Position('Test1', 2.555, 1));
        $newPosition = DatePosition::fromArray($oldPosition->jsonSerialize());

        $this->assertEquals($oldPosition->date(), $newPosition->date());
        $this->assertEquals($oldPosition->name(), $newPosition->name());
        $this->assertEquals($oldPosition->price(), $newPosition->price());
        $this->assertEquals($oldPosition->amount(), $newPosition->amount());
        $this->assertEquals($oldPosition->quantity(), $newPosition->quantity());
    }

    /** @test **/
    public function it_provides_formatted_values()
    {
        $position = new DatePosition($date = new DateTime, new Position('test', 1, 1));
        $position->setFormatter(new Formatter('de_DE'));

        $this->assertEquals($date->format('d.m.Y'), $position->format('date'));
    }
}
