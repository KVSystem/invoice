<?php

namespace Proengeno\Invoice\Test;

use DateTime;
use Proengeno\Invoice\Test\TestCase;
use Proengeno\Invoice\Positions\MonthlyBasePosition;

class MonthlyBasePositionTest extends TestCase
{
    /** @test **/
    public function it_sets_the_quantity_as_months_from_the_period()
    {
        $from = new DateTime("2019-01-01");
        $until = new DateTime("2019-12-31");
        $quantity = 12 / 365 * ($until->diff($from)->days + 1);

        $position = new MonthlyBasePosition('Test1', 1200, $from, $until);

        $this->assertEquals($quantity, $position->quantity());
        $this->assertEquals($position->amount() / $position->quantity() * 12, $position->yearlyAmount());
    }
}
