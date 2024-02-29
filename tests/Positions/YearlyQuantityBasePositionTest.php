<?php

namespace Proengeno\Invoice\Test;

use DateTime;
use Proengeno\Invoice\Positions\DayQuantityBasePosition;
use Proengeno\Invoice\Test\TestCase;

class DayQuantityBasePositionTest extends TestCase
{
    public function test_provide_the_positions_details(): void
    {
        $from = new DateTime("2019-01-01");
        $until = new DateTime("2019-02-31");

        $position = new DayQuantityBasePosition('Test1', 10, 1200, $from, $until);

        $this->assertEquals('Test1', $position->name());
        $this->assertEquals(3650.0, $position->price());
        $this->assertEquals(3650.0, $position->priceYearlyBased());
        $this->assertEquals(10.0, $position->priceDayBased());
        $this->assertEquals(1200.0, $position->quantity());
        $this->assertEquals($from, $position->from());
        $this->assertEquals($until, $position->until());
    }

    /** @test **/
    public function it_calculate_a_leep_year(): void
    {
        $from = new DateTime("2024-01-01");
        $until = new DateTime("2024-12-31");

        $position = new DayQuantityBasePosition('Test1', 10, 1200, $from, $until);

        $this->assertEquals(4392000, $position->amount());
    }

    /** @test **/
    public function it_calculate_a_partial_leep_year(): void
    {
        $from = new DateTime("2024-01-01");
        $until = new DateTime("2024-01-31");

        $position = new DayQuantityBasePosition('Test1', 10, 1200, $from, $until);

        $this->assertEquals(372000, $position->amount());
    }
}
