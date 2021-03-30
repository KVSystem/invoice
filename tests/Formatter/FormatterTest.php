<?php

namespace Proengeno\Invoice\Test\Formatter;

use DateTime;
use Proengeno\Invoice\Test\TestCase;
use Proengeno\Invoice\Positions\Position;
use Proengeno\Invoice\Formatter\Formatter;
use Proengeno\Invoice\Formatter\DateFormatter;
use Proengeno\Invoice\Positions\PeriodPosition;
use Proengeno\Invoice\Formatter\FloatFormatter;
use Proengeno\Invoice\Formatter\IntegerFormatter;
use Proengeno\Invoice\Formatter\DateIntervalFormatter;
use Proengeno\Invoice\Test\Fakes\TypeFormatter as FakeFormatter;

class FormatterTest extends TestCase
{
    /** @test **/
    public function it_formats_interger_values_with_de_locale_as_euro()
    {
        $formatter = new Formatter('de_DE');

        $this->assertEquals('1,00 €', $formatter->format(new Position('test', 1, 1), 'amount'));
    }

    /** @test **/
    public function it_can_multipie_values()
    {
        $formatter = new Formatter('de_DE', ['test' => ['price:multiplier' => 100, 'amount:multiplier' => 100]]);

        $this->assertEquals('100,00 €', $formatter->format(new Position('test', 1, 1), 'price'));
        $this->assertEquals('100,00 €', $formatter->format(new Position('test', 1, 1), 'amount'));
    }

    /** @test **/
    public function it_can_overwrites_the_default_pattern()
    {
        $formatter = new Formatter('de_DE', ['test' => [
            'amount:pattern' => '#,##0.00 $',
            'period:pattern' => '%a Monate',
            'until:pattern' => 'Y/m/d',
            'price:multiplier' => 10,
            'amount:multiplier' => 100,
        ]]);
        $position = new PeriodPosition('test', 1, 1, new DateTime('2019-01-01'), new DateTime('2019-12-31'));

        $this->assertEquals('10,00 €', $formatter->format($position, 'price'));
        $this->assertEquals('100,00 $', $formatter->format($position, 'amount'));
        $this->assertEquals('2019/12/31', $formatter->format($position, 'until'));
        $this->assertEquals('365 Monate', $formatter->format($position, 'period'));
    }

    /** @test **/
    public function it_can_overwrites_the_default_float_formatter()
    {
        $formatter = new Formatter('de_DE');

        $this->assertEquals('1,00 €', $formatter->format(new Position('test', 1, 1), 'price'));
    }

    /** @test **/
    public function it_can_format_datetimes()
    {
        $formatter = new Formatter('de_DE');
        $position = new PeriodPosition('test', 1, 1, new DateTime('2019-01-01'), new DateTime('2019-12-31'));
        $this->assertEquals('31.12.2019', $formatter->format($position, 'until'));
    }

    /** @test **/
    public function it_can_format_intervals()
    {
        $formatter = new Formatter('de_DE');
        $position = new PeriodPosition('test', 1, 1, new DateTime('2019-01-01'), new DateTime('2019-12-31'));
        $this->assertEquals('365 Tage', $formatter->format($position, 'period'));
    }
}
