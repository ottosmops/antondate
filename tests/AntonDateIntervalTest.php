<?php

namespace Ottosmops\Antondate\Tests;

use Ottosmops\Antondate\Tests\TestCase;
use Ottosmops\Antondate\ValueObjects\AntonDateInterval;

class AntonDateIntervalTest extends TestCase
{
    public $valid = ['973/1002-03', '0000/1972', '1971/ca. 1973', '973-1044', '1972/0000' ];
    public $invalid = ['1973-13/88', 'hallo', '1973/ca. 1971', '1973'];


    public function test_valid_AntonDatesIntervals()
    {
        foreach ($this->valid as $interval) {
            $this->assertTrue(AntonDateInterval::isValidString($interval));
        }
    }

    public function test_invalid_AntonDatesIntervals()
    {
        foreach ($this->invalid as $interval) {
            $this->assertFalse(AntonDateInterval::isValidString($interval));
        }
    }

    public function test_parse_valid_date_intervals()
    {
        foreach ($this->valid as $interval) {
            $antonDateInterval = AntonDateInterval::createFromString($interval);
            $this->assertInstanceOf(AntonDateInterval::class, $antonDateInterval);
        }
    }

    public function test_parse_invalid_date_intervals()
    {
        foreach ($this->invalid as $interval) {
            $this->expectException('InvalidArgumentException');
            $antonDateInterval = AntonDateInterval::createFromString($interval);
        }
    }

    public function test_anton_date_intervals_to_string()
    {
        foreach (['ca. 1947/1999-01-03'] as $interval) {
            $antonDateInterval = AntonDateInterval::createFromString($interval);
            $this->assertEquals('ca. 1947/1999-01-03', $antonDateInterval->toString());
            $this->assertEquals(1, $antonDateInterval->dateStartca());
            $this->assertEquals('1947-00-00', $antonDateInterval->mysqlDateStart());
            $this->assertEquals(0, $antonDateInterval->dateEndca());
            $this->assertEquals('1999-01-03', $antonDateInterval->mysqlDateEnd());

            $this->assertEqualsCanonicalizing(
                [
                    'date_start' => '1947-00-00',
                    'date_start_ca' => 1,
                    'date_end' => '1999-01-03',
                    'date_end_ca' => 0
                ], $antonDateInterval->toArray()
            );
        }
    }

    public function test_render_date()
    {
        \App::setLocale('de');
        $actual = AntonDateInterval::compose('0000-00-00', '0', '2002-03-31', '1')->renderDate();
        $expected = "ohne Datum – ca. 31. Mär 2002";
        $this->assertEquals($expected, $actual);

        \App::setLocale('en');
        $actual = AntonDateInterval::compose('0000-00-00', '0', '2002-03-31', '1')->renderDate();
        $expected = "no date – ca. 31st Mar 2002";
        $this->assertEquals($expected, $actual);

        \App::setLocale('fr');
        $actual = AntonDateInterval::compose('0000-00-00', '0', '2002-06-02', '1')->renderDate();
        $expected = 'pas date – ca. 2 juin 2002';
        $this->assertEquals($expected, $actual);

        \App::setLocale('fr');
        $actual = AntonDateInterval::compose('0000-00-00', '0', '2002-07-01', '1')->renderDate();
        $expected = 'pas date – ca. 1<sup>er</sup> juill. 2002';
        $this->assertEquals($expected, $actual);

    }


}
