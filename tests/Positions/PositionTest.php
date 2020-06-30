<?php

namespace Proengeno\Invoice\Test\Positions;

use Proengeno\Invoice\Test\TestCase;
use Proengeno\Invoice\Positions\Position;
use Proengeno\Invoice\Formatter\Formatter;

class PositionTest extends TestCase
{
    /** @test **/
    public function it_provides_the_position_name()
    {
        $position = new Position('test', 0, 0);

        $this->assertEquals('test', $position->name());
    }

    /** @test **/
    public function it_provides_the_given_quantity_price_as_float()
    {
        $position = new Position('test',1.22, 1);

        $this->assertEquals(1.22, $position->price());
    }

    /** @test **/
    public function it_provides_the_given_quantity_as_float()
    {
        $position = new Position('test',1, 1.55);

        $this->assertEquals(1.55, $position->quantity());
    }

    /** @test **/
    public function it_always_provides_the_pruduct_price_as_commercial_roundet_int()
    {
        $position = new Position('test',1.55555, 100);

        $this->assertEquals(15556, $position->amount());
    }

    /** @test **/
    public function it_can_serialize_the_parameter_to_jsons()
    {
        $position = new Position('test', 1.55555, 100);

        $this->assertJsonStringEqualsJsonString(
            json_encode(['name' => 'test', 'quantity' => 100, 'price' => 1.55555, 'amount' => 15556]),
            json_encode($position)
        );
    }

    /** @test **/
    public function it_can_build_from_an_array()
    {
        $oldPosition = new Position('test', 1.55555, 100);
        $newPosition = Position::fromArray($oldPosition->jsonSerialize());

        $this->assertEquals($oldPosition->name(), $newPosition->name());
        $this->assertEquals($oldPosition->price(), $newPosition->price());
        $this->assertEquals($oldPosition->amount(), $newPosition->amount());
        $this->assertEquals($oldPosition->quantity(), $newPosition->quantity());
    }
}
