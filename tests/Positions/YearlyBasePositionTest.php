<?php

namespace Proengeno\Invoice\Test;

use DateTime;
use Proengeno\Invoice\Test\TestCase;
use Proengeno\Invoice\Positions\YearlyBasePosition;

class YearlyBasePositionTest extends TestCase
{
    /** @test **/
    public function it_sets_the_quantity_as_years_from_the_period()
    {
        $from = new DateTime("2019-01-01");
        $until = new DateTime("2019-12-31");
        $quantity = 1 / 365 * ($until->diff($from)->days + 1);

        $position = new YearlyBasePosition('Test1', 1200, $from, $until);

        $this->assertEquals($quantity, $position->quantity());
    }

    /** @test **/
    public function it_calculate_a_leep_year(): void
    {
        $from = new DateTime("2024-01-01");
        $until = new DateTime("2024-12-31");

        $position = new YearlyBasePosition('Test1', 1200, $from, $until);

        $this->assertEquals(1200, $position->amount());
    }

    /** @test **/
    public function it_calculate_a_partial_leep_year(): void
    {
        $from = new DateTime("2024-01-01");
        $until = new DateTime("2024-01-31");

        $position = new YearlyBasePosition('Test1', 1200, $from, $until);

        $result = round(1200 * 1 / 366 * ($from->diff($until)->days + 1), 2);

        $this->assertEquals($result, $position->amount());
    }
}
