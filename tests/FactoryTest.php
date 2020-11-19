<?php

namespace Proengeno\Invoice\Test;

use Proengeno\Invoice\Invoice;
use Proengeno\Invoice\Factory;
use Proengeno\Invoice\Positions\Position;
use Proengeno\Invoice\Test\TestCase;

class FactoryTest extends TestCase
{
    /** @test **/
    public function it_adds_a_net_positions()
    {
        $factory = (new Factory)
            ->addNetPosition(19.0, new Position('price', 2.0, 3.0))
            ->addNetPosition(17.0, new Position('price', 2.0, 3.0));

        $this->assertCount(0, $factory->build()->grossPositions());
        $this->assertCount(2, $factory->build()->netPositions());

        return $factory;
    }

    /**
     * @test
     * @depends it_adds_a_net_positions
     **/
    public function it_adds_a_net_positions_from_an_array(factory $factory)
    {
        $factory->addNetPositionArray(16.0, [new Position('price', 2.0, 3.0), new Position('priceTwo', 2.0, 3.0)]);

        $this->assertCount(0, $factory->build()->grossPositions());
        $this->assertCount(4, $factory->build()->netPositions());

        return $factory;
    }

    /**
     * @test
     * @depends it_adds_a_net_positions_from_an_array
     **/
    public function it_adds_a_gross_position(factory $factory)
    {
        $factory->addGrossPosition(7.0, new Position('price', 2.0, 3.0))
            ->addGrossPosition(7.0, new Position('price', 2.0, 3.0));

        $this->assertCount(4, $factory->build()->netPositions());
        $this->assertCount(2, $factory->build()->grossPositions());

        return $factory;
    }

    /**
     * @test
     * @depends it_adds_a_gross_position
     **/
    public function it_adds_a_gross_position_from_an_array(factory $factory)
    {
        $factory->addGrossPositionArray(7.0, [new Position('price', 2.0, 3.0), new Position('price', 2.0, 3.0)]);

        $this->assertCount(4, $factory->build()->netPositions());
        $this->assertCount(4, $factory->build()->grossPositions());

        return $factory;
    }
}
