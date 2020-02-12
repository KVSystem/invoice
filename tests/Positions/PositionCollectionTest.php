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
    public function it_filters_the_positions_by_the_given_name()
    {
        $collection = new PositionCollection(
            new Position('one', 1, 1),
            new Position('one', 1, 1),
            new Position('two', 1, 1)
        );

        $this->assertCount(2, $collection->only('one'));
        $this->assertCount(1, $collection->except('one'));
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
            $this->assertCount(2, $collection->group()[$key]);
        }
    }
}
