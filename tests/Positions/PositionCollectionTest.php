<?php

namespace Proengeno\Invoice\Test\Positions;

use Proengeno\Invoice\Test\TestCase;
use Proengeno\Invoice\Positions\Position;
use Proengeno\Invoice\Positions\PositionCollection;

class PositionCollectionTest extends TestCase
{
    /** @test **/
    public function it_provides_all_fiven_positions_as_an_array()
    {
        $test = new PositionCollection(new Position('test', 1, 1), new Position('test', 1, 1));

        $this->assertTrue(is_array($test->all()));
        $this->assertCount(2, $test->all());
    }

    /** @test **/
    public function it_can_add_postitions_to_an_existing_collection()
    {
        $test = new PositionCollection(new Position('test', 1, 1));
        $test->add(new Position('test', 1, 1));

        $this->assertCount(2, $test);
    }

    /** @test **/
    public function it_merges_two_collection_together()
    {
        $collectionOne = new PositionCollection(new Position('test', 1, 1), new Position('test', 1, 1));
        $collectionTwo = new PositionCollection(new Position('test', 1, 1), new Position('test', 1, 1));

        $this->assertCount(4, $collectionOne->merge($collectionTwo));
    }

    /** @test **/
    public function it_sums_the_total_amount_of_the_given_key()
    {
        $collection = new PositionCollection(
            new Position('one', 1, 1),
            new Position('two', 2, 1)
        );

        $this->assertEquals(300, $collection->sum('amount'));
    }

    /** @test **/
    public function it_provides_the_minimum_value_of_the_given_key()
    {
        $collection = new PositionCollection(
            new Position('one', 1, 1),
            new Position('two', 2, 1)
        );

        $this->assertEquals(100, $collection->min('amount'));
    }

    /** @test **/
    public function it_provides_the_maximum_value_of_the_given_key()
    {
        $collection = new PositionCollection(
            new Position('one', 1, 1),
            new Position('two', 2, 1)
        );

        $this->assertEquals(200, $collection->max('amount'));
    }

    /** @test **/
    public function it_filters_the_positions_by_the_given_name()
    {
        $collection = new PositionCollection(
            new Position('one', 1, 1),
            new Position('one', 1, 1),
            new Position('two', 1, 1)
        );

        $this->assertCount(2, $collection->only('one'));
        $this->assertCount(2, $collection->only(function($position) { return $position->name() == 'one'; }));
        $this->assertCount(1, $collection->except('one'));
        $this->assertCount(1, $collection->except(function($position) { return $position->name() == 'one'; }));
    }

    /** @test **/
    public function it_groups_the_positions_by_name()
    {
        $collection = new PositionCollection(
            new Position('one', 1, 1),
            new Position('one', 1, 1),
            new Position('two', 1, 1),
            new Position('two', 2, 1)
        );

        foreach (['one', 'two'] as $key) {
            $this->assertCount(2, $collection->group('name')[$key]);
        }
    }

    /** @test **/
    public function it_can_build_from_an_array()
    {
        $collectionOne = new PositionCollection(
            new Position('one', 1, 1),
            new Position('one', 1, 1),
            new Position('two', 1, 1),
            new Position('two', 2, 1)
        );
        $collectionClone = PositionCollection::fromArray($collectionOne->jsonSerialize());

        for ($i = 0; $i < count($collectionOne); $i++) {
            $this->assertEquals($collectionOne[$i]->name(), $collectionClone[$i]->name());
            $this->assertEquals($collectionOne[$i]->price(), $collectionClone[$i]->price());
            $this->assertEquals($collectionOne[$i]->amount(), $collectionClone[$i]->amount());
            $this->assertEquals($collectionOne[$i]->quantity(), $collectionClone[$i]->quantity());
        }
    }
}
