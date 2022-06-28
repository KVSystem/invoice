<?php

namespace Proengeno\Invoice\Test\Positions;

use Proengeno\Invoice\Test\TestCase;
use Proengeno\Invoice\Positions\Position;
use Proengeno\Invoice\Positions\PositionGroup;
use Proengeno\Invoice\Interfaces\Position as PositionInterface;

class PositionGroupTest extends TestCase
{
    /** @test **/
    public function it_tell_wether_it_has_vat_or_not()
    {
        $test = new PositionGroup(PositionGroup::NET, 19.0, [new Position('test', 1.55555, 100)]);

        $this->assertTrue($test->hasVat());
    }

    /** @test **/
    public function it_provides_the_group_type()
    {
        $net = new PositionGroup(PositionGroup::NET, 19.0, [new Position('test', 1.55555, 100)]);
        $gross = new PositionGroup(PositionGroup::GROSS, 19.0, [new Position('test', 1.55555, 100)]);

        $this->assertTrue($net->isNet());
        $this->assertFalse($net->isGross());

        $this->assertTrue($gross->isGross());
        $this->assertFalse($gross->isNet());
    }

    /** @test **/
    public function it_provides_the_positions_of_the_group()
    {
        $group = new PositionGroup(PositionGroup::NET, 19.0, [
            new Position('test', 1.55555, 100), new Position('test', 2, 100)
        ]);

        $this->assertCount(2, $group->positions());
    }

    /** @test **/
    public function it_provides_the_vat_of_a_the_group()
    {
        $group = new PositionGroup(PositionGroup::NET, 19.0, [
            new Position('test', 1.55555, 100), new Position('test', 2, 100)
        ]);

        $this->assertEquals(19.0, $group->vatPercent());
    }

    /** @test **/
    public function it_computes_the_total_amounts_of_the_group()
    {
        $net = new PositionGroup(PositionGroup::NET, 19.0, [
            new Position('test', 1.55555, 100), new Position('test', 2, 100)
        ]);

        $gross = new PositionGroup(PositionGroup::GROSS, 19.0, [
            new Position('test', 1.55555, 100), new Position('test', 2, 100)
        ]);

        $this->assertEquals($net->grossAmount(), $net->netAmount() + $net->vatAmount());
        $this->assertEquals($gross->grossAmount(), $gross->netAmount() + $gross->vatAmount());
    }

    /** @test **/
    public function it_can_iterate_over_the_positions()
    {
        $group = new PositionGroup(PositionGroup::NET, 19.0, [
            new Position('test', 1.55555, 100), new Position('test', 2, 100)
        ]);

        foreach ($group as $position) {
            $this->assertInstanceOf(PositionInterface::class, $position);
        }
    }

    /** @test **/
    public function it_has_array_like_acces_positions()
    {
        $group = new PositionGroup(PositionGroup::NET, 19.0, [
            new Position('test', 1.55555, 100), new Position('test', 2, 100)
        ]);

        $this->assertInstanceOf(PositionInterface::class, $group[0]);
        $this->assertTrue(isset($group[1]));
        $this->assertCount(2, $group);
    }

    /** @test **/
    public function it_can_build_from_an_array()
    {
        $group = new PositionGroup(PositionGroup::NET, 19.0, [
            new Position('test', 1.55555, 100), new Position('test', 2, 100)
        ]);
        $groupClone = PositionGroup::fromArray($group->jsonSerialize());

        for ($i = 0; $i < count($group); $i++) {
            $this->assertEquals($group->grossAmount(), $groupClone->grossAmount());
            $this->assertEquals($group->vatAmount(), $groupClone->vatAmount());
            $this->assertEquals($group->netAmount(), $groupClone->netAmount());
        }
    }

    /** @test **/
    public function it_can_add_postions_to_the_group()
    {
        $group = new PositionGroup(PositionGroup::NET, 19.0, [
            new Position('test', 1, 100), new Position('test', 2, 100)
        ]);

        $newGroup = $group->withPosition(new Position('test', 3, 100));

        $this->assertSame(2, $group->count());
        $this->assertSame(3, $newGroup->count());
    }
}
