<?php

namespace Proengeno\Invoice\Test;

use DateTime;
use Proengeno\Invoice\Test\TestCase;
use Proengeno\Invoice\Positions\YearlyQuantityBasePosition;

class YearlyQuantityBasePositionTest extends TestCase
{
    /** @test **/
    public function it_sets_calculates_the_amount_based_on_days_in_year_and_quantity(): void
    {
        $from = new DateTime("2019-01-01");
        $until = new DateTime("2019-02-31");

        $position = new YearlyQuantityBasePosition('Test1', 10, 1200, $from, $until);

        $this->assertEquals(
            round(1200 * 10 / 365 * ($until->diff($from)->days + 1), 2), $position->amount()
        );
    }

    public function test_provide_the_positions_details(): void
    {
        $from = new DateTime("2019-01-01");
        $until = new DateTime("2019-02-31");

        $position = new YearlyQuantityBasePosition('Test1', 10, 1200, $from, $until);

        $this->assertEquals('Test1', $position->name());
        $this->assertEquals(10, $position->price());
        $this->assertEquals(10, $position->priceYearlyBased());
        $this->assertEquals(0.02739726027397, $position->priceDayBased());
        $this->assertEquals(1200.0, $position->quantity());
        $this->assertEquals($from, $position->from());
        $this->assertEquals($until, $position->until());
    }
}
