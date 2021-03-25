<?php

namespace Proengeno\Invoice\Test\Collections;

use Proengeno\Invoice\Collections\GroupCollection;
use Proengeno\Invoice\Positions\PositionGroup;
use Proengeno\Invoice\Positions\Position;
use Proengeno\Invoice\Test\TestCase;

class GroupCollectionTest extends TestCase
{
    private GroupCollection $defaultGroupCollection;

    public function setUp(): void
    {
        $this->defaultGroupCollection = new GroupCollection(
            new PositionGroup('net', 19.0, [new Position('test', 1, 1)]),
            new PositionGroup('net', 16.0, [new Position('test', 1, 1)])
        );
    }

    /** @test **/
    public function it_provides_all_given_groups_as_an_array()
    {
        $this->assertTrue(is_array($this->defaultGroupCollection->all()));
        $this->assertCount(2, $this->defaultGroupCollection->all());
    }

    /** @test **/
    public function it_sums_the_total_gross_amount()
    {
        $this->assertEquals(2.35, $this->defaultGroupCollection->sumGrossAmount());
    }

    /** @test **/
    public function it_sums_the_total_net_amount()
    {
        $this->assertEquals(2.0, $this->defaultGroupCollection->sumNetAmount());
    }

    /** @test **/
    public function it_sums_the_total_vat_amount()
    {
        $this->assertEquals(0.35, $this->defaultGroupCollection->sumVatAmount());
    }

    /** @test **/
    public function it_filters_the_groups()
    {
        $this->assertCount(1,
            $this->defaultGroupCollection->filter(fn(PositionGroup $group) => $group->vatPercent() === 19.0)
        );
        $this->assertCount(2,
            $this->defaultGroupCollection->filter(fn(PositionGroup $group) => $group->isNet())
        );
        $this->assertCount(0,
            $this->defaultGroupCollection->filter(fn(PositionGroup $group) => $group->isGross())
        );
    }

    /** @test **/
    public function it_sorts_the_groups_by_the_given_key()
    {
        $this->assertEquals(19.0, $this->defaultGroupCollection[0]->vatPercent());
        $this->assertEquals(16.0, $this->defaultGroupCollection[1]->vatPercent());

        $sortedGroups = $this->defaultGroupCollection->sort(fn(PositionGroup $group) => $group->vatPercent());

        $this->assertEquals(16.0, $sortedGroups[0]->vatPercent());
        $this->assertEquals(19.0, $sortedGroups[1]->vatPercent());
    }

    /** @test **/
    public function it_groups_the_groups()
    {
        $groupedGroups = $this->defaultGroupCollection->group(fn(PositionGroup $group) => $group->vatPercent());

        $this->assertInstanceOf(GroupCollection::class, $groupedGroups['16']);
        $this->assertInstanceOf(GroupCollection::class, $groupedGroups['19']);
        $this->assertCount(1, $groupedGroups['16']);
        $this->assertCount(1, $groupedGroups['19']);
    }

    /** @test **/
    public function it_maps_the_groups()
    {
        $this->assertEmpty(
            array_diff([19.0, 16.0], $this->defaultGroupCollection->map(fn(PositionGroup $group) => $group->vatPercent()))
        );
    }
}
