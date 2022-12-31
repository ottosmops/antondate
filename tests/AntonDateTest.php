<?php

namespace Ottosmops\Antondate\Tests;

use Ottosmops\Antondate\Tests\TestCase;
use Ottosmops\Antondate\ValueObjects\AntonDate;

class AntonDateTest extends TestCase
{
    public $validDates = ['0000', '973', '0000-00-00', '1973', '1973-00-00',
                          '1973-01', '1902-12', '1973-01-00', '1973-01-05', 'ca. 1973',
                          '0000-00-03', '973', '-200'];
    public $invalidDates = ['1973-13', '73-04-01', '1973.00', '02','1977-00-01'];

    public function testAntonDatesAreValid()
    {
        foreach ($this->validDates as $date) {
            $this->assertTrue(AntonDate::isValidString($date));
        }
    }

    public function testNonAntonDatesAreInvalid()
    {
        foreach ($this->invalidDates as $date) {
            $this->assertFalse(AntonDate::isValidString($date));
        }
    }

    public function testComposeAntonDateToArray()
    {
        $actual = AntonDate::compose('1973', '12', '01', 1)->toArray();
        $expected = ['year' => '1973', 'month' => '12', 'day' => '1', 'ca' => 1];

        $this->assertEquals($expected, $actual);
    }

    public function testTodayIsAntonDate()
    {
        $actual = AntonDate::today();
        $this->assertInstanceOf(\Ottosmops\Antondate\ValueObjects\AntonDate::class, $actual);
    }

    public function testComposeAntonDateToString()
    {
        $actual = AntonDate::compose('1973', '12', '01', 1)->toString();
        $expected = 'ca. 1973-12-01';
        $this->assertEquals($expected, $actual);
    }
    /**
     * @expectException InvalidArgumentException
     * @expectExceptionMessage AntonDate is not valid: (1973, 13, 01, 1)
     */
    public function testComposeInvalidAntonDate()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('AntonDate is not valid: (1973, 13, 1, 1)');
        AntonDate::compose('1973', '13', '01', 1);
    }

    public function testComposeYearToString()
    {
        $actual   = AntonDate::compose('1973')->toString();
        $expected = '1973';
        $this->assertEquals($expected, $actual);
    }

    public function testComposeZeroToFormatted()
    {
        $actual   = AntonDate::compose('0000')->formatted();
        $expected = trans('antondate::antondate.no_date');
        $this->assertEquals($expected, $actual);
    }

    public function testComposeYearToFormatted()
    {
        $actual   = AntonDate::compose('1971')->formatted('', 'YY');
        $expected = '71';
        $this->assertEquals($expected, $actual);
    }

    public function testComposeYearMonthToFormatted()
    {
        $actual   = AntonDate::compose('1971', '03')->formatted('en', '', 'MMMM YYYY');
        $expected = 'March 1971';
        $this->assertEquals($expected, $actual);
    }

    public function testComposeYearMonthToFormattedWithFebruar()
    {
        $actual   = AntonDate::compose('1971', '02')->formatted('en', '', 'MMMM YYYY');
        $expected = 'February 1971';
        $this->assertEquals($expected, $actual);
    }

     public function testComposeYearMonthDayToFormattedFrench()
     {
        $this->setLocale(LC_ALL, 'fr_FR');
        $actual   = AntonDate::compose('1971', '03', '01')->formatted('fr');
        $expected = '1er mars 1971';
        $this->assertEquals($expected, $actual);

        //$str = '1971-03-01';
        //$date = Carbon::parse($str)->locale('fr');
        //$actual = $date->isoFormat('Do MMM YYYY');
        //$expected = '1er mars 1971';
        //setlocale(LC_ALL, 'en_US');
        //$this->assertEquals($expected, $actual);
     }

    public function testComposeYearMonthDayToFormattedEnglish()
    {
        setlocale(LC_ALL, 'en_US');
        $actual   = AntonDate::compose('1971', '03', '01')->formatted('en');
        $expected = '1st Mar 1971';
        $this->assertEquals($expected, $actual);
    }

    public function testComposeYearMonthDayToFormattedGerman()
    {
        \App::setLocale('de');
        $actual   = AntonDate::compose('1971', '03', '01')->formatted();
        $expected = '1. Mär 1971';
        $this->assertEquals($expected, $actual);
    }

    public function testFormatDate()
    {
        \App::setLocale('de');
        $actual = AntonDate::createFromString(0)->formatDate(true);
        $expected = '';
        $this->assertEquals($expected, $actual);

        $actual = AntonDate::createFromString(0)->formatDate(false);
        $expected = 'ohne Datum';
        $this->assertEquals($expected, $actual);
    }

    public function testComposeYearToMysql()
    {
        $actual   = AntonDate::compose('1973')->toMysqlDate();
        $expected = '1973-00-00';
        $this->assertEquals($expected, $actual);
    }

    public function testIsEqualTo()
    {
        $date1 = AntonDate::createFromString('1973-03-01');
        $date2 = AntonDate::compose('1973', '03', '01', 0);
        $this->assertTrue($date1->isEqualTo($date2));
    }

    public function testIsEqualToWithCa()
    {
        $date1 = AntonDate::createFromString('1973-03-01');
        $date2 = AntonDate::compose('1973', '03', '01', 1);
        $this->assertFalse($date1->isEqualTo($date2, true));
    }

    public function testIsGreaterThan()
    {
        $date1 = AntonDate::createFromString('1773-03-01');
        $date2 = AntonDate::createFromString('1771');
        $this->assertTrue($date1->isGreaterThan($date2));
    }

    public function testIsGreaterThan2()
    {
        $date1 = AntonDate::createFromString('303-03-01');
        $date2 = AntonDate::createFromString('301');
        $this->assertTrue($date1->isGreaterThan($date2));
    }

     public function testgetYear()
    {
        $date1 = AntonDate::createFromString('1773-03-01');
        $this->assertEquals(1773, $date1->getYear());
        $date2 = AntonDate::createFromString('1771');
        $this->assertEquals(1771, $date2->getYear());
        $date2 = AntonDate::createFromString('301');
        $this->assertEquals(301, $date2->getYear());
    }

    public function testIsGreaterReturnsTrueForZero()
    {
        $date1 = AntonDate::createFromString('0000-00-00');
        $this->assertTrue($date1->isGreaterThan(AntonDate::createFromString('1773-03-01')));

        $date2 = AntonDate::createFromString('1773-03-01');
        $this->assertTrue($date2->isGreaterThan(AntonDate::createFromString('0000-00-00')));
    }

    public function testIsLessReturnsTrueForZero()
    {
        $date1 = AntonDate::createFromString('0000-00-00');
        $this->assertTrue($date1->isLessThan(AntonDate::createFromString('1773-03-01')));

        $date2 = AntonDate::createFromString('1773-03-01');
        $this->assertTrue($date2->isLessThan(AntonDate::createFromString('0000-00-00')));
    }

    public function testGuessDateFromString()
    {
        $actual = AntonDate::guessFromString('2. April 2014');
        $expected = '2014-04-02';
        $this->assertEquals($expected, $actual);

        $actual = AntonDate::guessFromString('2.4.2014');
        $expected = '2014-04-02';
        $this->assertEquals($expected, $actual);

        $actual = AntonDate::guessFromString('2. Apr. 2014');
        $expected = '2014-04-02';
        $this->assertEquals($expected, $actual);
    }
}
