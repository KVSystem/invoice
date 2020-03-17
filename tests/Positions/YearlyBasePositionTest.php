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
}
