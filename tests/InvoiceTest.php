<?php

namespace Proengeno\Invoice\Test;

use DateTime;
use Proengeno\Invoice\Invoice;
use Proengeno\Invoice\Test\TestCase;
use Proengeno\Invoice\Positions\Position;
use Proengeno\Invoice\Formatter\Formatter;
use Proengeno\Invoice\Positions\PositionGroup;
use Proengeno\Invoice\Positions\PeriodPosition;

class InvoiceTest extends TestCase
{
    /** @test **/
    public function it_provides_the_total_net_amount()
    {
        $invoice = new Invoice([
            new PositionGroup(PositionGroup::NET, 19.0, [new Position('price', 2.0, 3.0)]),
            new PositionGroup(PositionGroup::NET, 19.0, [new Position('price', 2.0, 3.0)])
        ]);

        $this->assertEquals(2*3*2, $invoice->netAmount());
    }

    /** @test **/
    public function it_provides_the_total_gross_amount()
    {
        $invoice = new Invoice([
            new PositionGroup(PositionGroup::NET, 19.0, [new Position('price', 2.0, 3.0)])
        ]);

        $this->assertEquals(2*3*1.19, $invoice->grossAmount());
    }

    /** @test **/
    public function it_provides_the_total_vat_amount()
    {
        $invoice = new Invoice([
            new PositionGroup(PositionGroup::NET, 19.0, [new Position('price', 2.0, 3.0)])
        ]);

        $this->assertEquals($invoice->grossAmount() - $invoice->netAmount(), $invoice->vatAmount());
    }

    /** @test **/
    public function it_provides_positon_groups()
    {
        $invoice = new Invoice([
            $group = new PositionGroup(PositionGroup::NET, 19.0, [new Position('price', 2.0, 3.0)]),
            new PositionGroup(PositionGroup::NET, 19.0, [new Position('price', 2.0, 3.0)])
        ]);

        $this->assertSame($group, $invoice->groups()[0]);
        $this->assertCount(2, $invoice->groups());
    }

    /** @test **/
    public function it_filters_the_positions_by_conditions()
    {
        $invoice = new Invoice([
            new PositionGroup(PositionGroup::NET, 19.0, [new Position('priceOne', 2.0, 3.0)]),
            new PositionGroup(PositionGroup::NET, 19.0, [new Position('priceOne', 2.0, 3.0)]),
            new PositionGroup(PositionGroup::NET, 19.0, [new Position('priceTwo', 2.0, 3.0)]),
            new PositionGroup(PositionGroup::GROSS, 19.0, [new Position('priceThree', 2.0, 3.0)])
        ]);

        $this->assertCount(3, $invoice->netPositions());
        $this->assertCount(2, $invoice->netPositions('priceOne'));
        $this->assertCount(1, $invoice->netPositions('priceTwo'));
        $this->assertCount(0, $invoice->netPositions('priceThree'));
        $this->assertCount(1, $invoice->grossPositions('priceThree'));
        $this->assertCount(3, $invoice->netPositions(['priceOne', 'priceTwo']));
    }

    /** @test **/
    public function it_can_build_from_an_array()
    {
        $invoice = new Invoice([
            new PositionGroup(PositionGroup::NET, 19.0, [new Position('price', 2.0, 3.0)]),
            new PositionGroup(PositionGroup::NET, 19.0, [new Position('price', 2.0, 3.0)])
        ]);

        $invoiceClone = Invoice::fromArray($invoice->jsonSerialize());

        $this->assertEquals($invoice->netAmount(), $invoiceClone->netAmount());
        $this->assertEquals($invoice->grossAmount(), $invoiceClone->grossAmount());
        $this->assertEquals($invoice->vatAmount(), $invoiceClone->vatAmount());
    }

    /** @test **/
    public function it_can_negate_itself()
    {
        $invoice = new Invoice([
            new PositionGroup(PositionGroup::NET, 19.0, [new Position('price', 2.0, 3.0)]),
            new PositionGroup(PositionGroup::NET, 19.0, [new Position('price', 2.0, 3.0)])
        ]);

        $invoiceNegation =  $invoice->negate();

        $this->assertEquals($invoice->netAmount() * -1, $invoiceNegation->netAmount());
        $this->assertEquals($invoice->grossAmount() * -1, $invoiceNegation->grossAmount());
        $this->assertEquals($invoice->vatAmount() * -1, $invoiceNegation->vatAmount());
    }

    /** @test **/
    public function it_provides_formatted_amounts()
    {
        $invoice = new Invoice([
            new PositionGroup(PositionGroup::NET, 19.0, [
                new PeriodPosition('priceOne', 2.50, 3.5, new DateTime('2019-01-01'), new DateTime('2019-12-31'))
            ])
        ]);
        $invoice->setFormatter(new Formatter('de_DE', [
            'priceOne' => ['price:pattern' => "#,##0.### Ct/kWh", 'price:multiplier' => 100]
        ]));
        $this->assertEquals('8,75 €', $invoice->format('netAmount'));
        $this->assertEquals('1,66 €', $invoice->format('vatAmount'));
        $this->assertEquals('10,41 €', $invoice->format('grossAmount'));
        $this->assertEquals('8,75 €', $invoice->groups()[0]->format('netAmount'));
        $this->assertEquals('1,66 €', $invoice->groups()[0]->format('vatAmount'));
        $this->assertEquals('10,41 €', $invoice->groups()[0]->format('grossAmount'));
        $this->assertEquals('8,75 €', $invoice->netPositions()->format('sum', ['amount']));
        $this->assertEquals('250 Ct/kWh', $invoice->netPositions()[0]->format('price'));
        $this->assertEquals('01.01.2019', $invoice->netPositions()[0]->format('from'));
        $this->assertEquals('365 Tage', $invoice->netPositions()[0]->format('period'));
        $this->assertEquals('3,5', $invoice->netPositions()[0]->format('quantity'));
        $this->assertEquals('8,75 €', $invoice->netPositions()[0]->format('amount'));
    }

    /** @test **/
    public function it_can_add_a_postions()
    {
        $invoice = new Invoice([
            new PositionGroup(PositionGroup::NET, 19.0, [new Position('price', 2.0, 3.0)]),
        ]);

        $newInvoiceWithSameGroup = $invoice->withPosition(PositionGroup::NET, 19.0, new Position('price', 2.0, 3.0));
        $newInvoiceWithNewGroup = $invoice->withPosition(PositionGroup::NET, 16.0, new Position('price', 2.0, 3.0));
        $newInvoiceWithAnotherGroup = $newInvoiceWithNewGroup->withPosition(PositionGroup::GROSS, 16.0, new Position('price', 2.0, 3.0));

        $this->assertSame(1, $invoice->netPositions()->count());
        $this->assertSame(0, $invoice->grossPositions()->count());
        $this->assertSame(1, $invoice->groups()->count());

        $this->assertSame(2, $newInvoiceWithSameGroup->netPositions()->count());
        $this->assertSame(0, $newInvoiceWithSameGroup->grossPositions()->count());
        $this->assertSame(1, $newInvoiceWithSameGroup->groups()->count());

        $this->assertSame(2, $newInvoiceWithNewGroup->netPositions()->count());
        $this->assertSame(0, $newInvoiceWithNewGroup->grossPositions()->count());
        $this->assertSame(2, $newInvoiceWithNewGroup->groups()->count());

        $this->assertSame(2, $newInvoiceWithAnotherGroup->netPositions()->count());
        $this->assertSame(1, $newInvoiceWithAnotherGroup->grossPositions()->count());
        $this->assertSame(3, $newInvoiceWithAnotherGroup->groups()->count());
    }
}
