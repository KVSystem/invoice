<?php

namespace Proengeno\Invoice\Test;

use DateTime;
use Proengeno\Invoice\Test\TestCase;
use Proengeno\Invoice\Positions\DayBasePosition;

class DayBasePositionTest extends TestCase
{
    /** @test **/
    public function it_sets_the_quantity_as_days_from_the_period()
    {
        $from = new DateTime("2019-01-01");
        $until = new DateTime("2020-01-01");
        $quantity = (float)$until->diff($from)->days + 1;

        $position = new DayBasePosition('Test1', 1200, $from, $until);

        $this->assertEquals($quantity, $position->quantity());
    }

    /** @test **/
    public function it_provides_the_given_date_start_date()
    {
        $from = new DateTime("2019-01-01");
        $until = new DateTime("2020-01-01");

        $position =  new DayBasePosition('Test1', 1200, $from, $until);

        $this->assertEquals($from, $position->from());
    }

    /** @test **/
    public function it_provides_the_given_date_end_date()
    {
        $from = new DateTime("2019-01-01");
        $until = new DateTime("2020-01-01");

        $position =  new DayBasePosition('Test1', 1200, $from, $until);

        $this->assertEquals($until, $position->until());
    }

    /** @test **/
    public function it_provides_the_position_name()
    {
        $from = new DateTime("2019-01-01");
        $until = new DateTime("2020-01-01");

        $position =  new DayBasePosition('test', 1200, $from, $until);

        $this->assertEquals('test', $position->name());
    }

    /** @test **/
    public function it_provides_the_given_quantity_price()
    {
        $from = new DateTime("2019-01-01");
        $until = new DateTime("2020-01-01");
        $position = new DayBasePosition('Test1', 1200, $from, $until);

        $this->assertEquals(1200, $position->price());
    }

    /** @test **/
    public function it_calculates_the_prucuct_of_the_quantity_an_the_price()
    {
        $from = new DateTime("2019-01-01");
        $until = new DateTime("2020-01-01");
        $position = new DayBasePosition('Test1', 1200, $from, $until);

        $this->assertEquals(1200 * $position->quantity(), (float)$position->amount());
    }
}
