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
    protected function tearDown(): void
    {
        Formatter::setDateIntervalFormatter(DateIntervalFormatter::class);
        Formatter::setIntergerFormatter(IntegerFormatter::class);
        Formatter::setFloatFormatter(FloatFormatter::class);
        Formatter::setDateFormatter(DateFormatter::class);
    }

    /** @test **/
    public function as_a_default_it_formats_interger_values_with_de_locale_as_euro()
    {
        $formatter = new Formatter('de_DE');

        $this->assertEquals('1,00 €', $formatter->format(new Position('test', 1, 1), 'amount'));
    }

    /** @test **/
    public function it_can_overwrites_the_default_pattern()
    {
        $formatter = new Formatter('de_DE', ['test' => ['amount:pattern' => '#,##0.00 $', 'until:pattern' => 'Y/m/d']]);
        $position = new PeriodPosition('test', 1, 1, new DateTime('2019-01-01'), new DateTime('2019-12-31'));

        $this->assertEquals('1,00 $', $formatter->format($position, 'amount'));
        $this->assertEquals('2019/12/31', $formatter->format($position, 'until'));
    }

    /** @test **/
    public function it_can_overwrites_the_default_float_formatter()
    {
        Formatter::setFloatFormatter(FakeFormatter::class);
        $formatter = new Formatter('de_DE');

        $this->assertEquals('FAKE:1', $formatter->format(new Position('test', 1, 1), 'price'));
    }

    /** @test **/
    public function it_can_overwrites_the_default_date_formatter()
    {
        Formatter::setDateFormatter(FakeFormatter::class);
        $formatter = new Formatter('de_DE');
        $position = new PeriodPosition('test', 1, 1, new DateTime('2019-01-01'), new DateTime('2019-12-31'));
        $this->assertEquals('FAKE:2019-12-31', $formatter->format($position, 'until'));
    }

    /** @test **/
    public function it_can_overwrites_the_default_date_interval_formatter()
    {
        Formatter::setDateIntervalFormatter(FakeFormatter::class);
        $formatter = new Formatter('de_DE');
        $position = new PeriodPosition('test', 1, 1, new DateTime('2019-01-01'), new DateTime('2019-12-31'));
        $this->assertEquals('FAKE:365', $formatter->format($position, 'period'));
    }
}
