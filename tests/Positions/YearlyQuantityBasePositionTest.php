<?php

namespace Proengeno\Invoice\Test;

use DateTime;
use Proengeno\Invoice\Test\TestCase;
use Proengeno\Invoice\Positions\YearlyQuantityBasePosition;

class YearlyQuantityBasePositionTest extends TestCase
{
    /** @test **/
    public function it_sets_calculates_the_amount_based_on_days_in_year_and_quantity()
    {
        $from = new DateTime("2019-01-01");
        $until = new DateTime("2019-12-31");

        $position = new YearlyQuantityBasePosition('Test1', 10, 1200, $from, $until);

        $this->assertEquals(1200 * 10, $position->amount());
    }
}
