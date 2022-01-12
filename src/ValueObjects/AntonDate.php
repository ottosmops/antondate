<?php

namespace Ottosmops\Antondate\ValueObjects;

use Carbon\Carbon;
use Ottosmops\Antondate\ValueObjects\ValueObjectInterface;

/**
 * AntonDate is the way to handle historical Dates within Anton.
 * An AntonDate is an ISO-Date in the format 'Y-m-d', 'Y-m', 'Y' or
 * '0000' (meaning, that no date is provided)
 * AntonDate accepts also a 'ca. ' in front of the Date.
 * It is yet not possible to handle BC dates.
 */
final class AntonDate implements ValueObjectInterface
{
    /** @const Regex for a AntonDateString */
    const ANTON_DATE_PATTERN = '(?:ca. )?(?:(-?\d{3,4})(?:-(\d{2}))?(?:-(\d{2}))?)';

    private $year = 0;

    private $month = 0;

    private $day = 0;

    private $ca = 0;


    // ********************
    // Named constructors
    // ********************

    /**
     * Returns a new AntonDate from a date-string
     *
     * @param  string|null $sDate in AntonDateFormat 'Y-m-d', 'Y-m', 'Y' or '0000'
     *                 with or wthout a 'ca. ' in front of the date
     * @param  boolean $ca if $sDate starts with 'ca. ' or $ca is true the AntonDate contains $ca == 1
     * @return object  AntonDate
     */
    public static function createFromString($sDate, $ca = 0) : AntonDate
    {
        $sDate = $sDate ?: '0000-00-00';
        self::checkBool($ca);

        $sDate = trim($sDate);

        if (!self::isValidString($sDate)) {
            throw new \InvalidArgumentException("Could not parse string to AntonDate $sDate");
        }

        if (strpos($sDate, trans('antondate::antondate.ca').' ') === 0) { //
            $ca = 1;
            $sDate = str_replace(trans('antondate::antondate.ca'), '', $sDate);
            $sDate = trim($sDate);
        }

        $sDate = self::addZeros($sDate); // make sure that we have a mysqlDateString

        $aDate = [];
        list($aDate['year'], $aDate['month'], $aDate['day'])= explode('-', $sDate);
        $aDate['ca'] = $ca;

        return new static($aDate);
    }

    /**
     * Returns a new AntonDate from a date-string which is freely formatted
     *
     * @param string $value
     * @return object AntonDate
     */
    public static function guessFromString(string $value) : AntonDate
    {
        $ca = 0;
        $value = trim($value);

        if (strpos($value, trans('messages.ca').' ') === 0) { //
            $ca = 1;
            $value = str_replace(trans('messages.ca'), '', $value);
            $value = trim($value);
        }

        if (preg_match('/\d+\.\d+\.\d{4}/', $value)) {
            $value = date('Y-m-d', strtotime($value));
        }

        if (preg_match('/(\d+)\.\s?([A-Za-zä]+)\.?\s+(\d{4})/', $value, $matches)) {
            $day    = trim($matches[1]);

            // DateTime::format()
            $months = static::getMonths();

            foreach ($months as $k => $v) {
                if (\Str::startsWith($v, $matches[2])) {
                    $month = $k;
                }
            }
            $year  = $matches[3];
            $value = date('Y-m-d', strtotime("$day-$month-$year"));
        }

        return static::createFromString($value, $ca);
    }

    /**
     * Returns a new AntonDate from a year, month, day, and ca
     *
     * @param  str $year  4 digits
     * @param  str $month 2 digits (1-12)
     * @param  str $day   2 digits (1-31)
     * @return obj returns an AntonDate-Object
     */
    public static function compose($year = '0000', $month = '00', $day = '00', $ca = 0) : AntonDate
    {
        self::checkBool($ca);
        $aDate['year']  = (int) $year; //str_pad($year, 4, "0", STR_PAD_LEFT);
        $aDate['month'] = (int) $month; //str_pad($month, 2, "0", STR_PAD_LEFT);
        $aDate['day']   = (int) $day; //str_pad($day, 2, "0", STR_PAD_LEFT);
        $aDate['ca']    = $ca;

        return new static($aDate);
    }

    /**
     * Returns a new AntonDate for today.
     * @return obj returns an AntonDate-Object
     */
    public static function today() : AntonDate
    {
        $today = date('Y-m-d');
        return self::createFromString($today);
    }

    // **********
    // Validator
    // **********

    /**
     * year must be 0000 or between 100-9999
     * month must be 00 or between 1-12
     * day must be 00 or between 1-31 depending on the month and the leap-year
     * if a date is set it can be preceeded by 'ca. '
     * 0000 or 000-00-00 means that no date can be provided
     * Valid: 1934, 1934-02, 1934-02-05 (this is the difference to MysqlDate)
     * Valid: 1934-00-00, 1934-00       (this is the difference to DateTime)
     * Valid: ca. 1934, ca. 1934-09
     * Valid: 0000                      (means no date)
     * Valid: 000-00-01, 0000-02-01
     * Invalid: 0000-00, 0000-01, 1934-00-01
     *
     * @param  string  $date
     * @return boolean
     */
    public static function isValidString(string $sDate)
    {
        $date = trim($sDate);

        if ($date == '0000' || $date == '0000-00-00') {
            return true;
        }

        if (strpos($date, trans('antondate::antondate.ca').' ') === 0) {
            $date = substr_replace($date, '', 0, 4);
        }

        $date = self::cleanDate($date);

        if (!preg_match('|^'.self::ANTON_DATE_PATTERN.'$|', $date, $match)) {
            return false;
        }

        $year  = $match[1];
        $month = (int) ($match[2] ?? 0);
        $day =  (int) ($match[3] ?? 0);

        if ($month >= 0 && $day > 0 && $year > 0) {
            return checkdate($month, $day, $year);
        }

        if (isset($match[2]) && $year > 0) {
            return (0 <= $match[2] && $match[2] < 13);
        }

        return ((-4714 <= $year) && ($year < 9999));
    }

    // *********************
    // getters and mutators
    // *********************

    /**
     * Returns the value.
     *
     * @return string
     */
    public function toString() : string
    {
        return (string) $this->__toString();
    }

    /**
     * Returns an associative array.
     * @return array ['year'=> , 'month'=> , 'day'=> , 'ca'=>]
     */
    public function toArray(bool $with_ca = true) : array
    {
        $date = [
            'year' => $this->year,
            'month' => $this->month,
            'day' => $this->day
        ];

        if ($with_ca) {
            $date['ca'] = $this->ca;
        }

        return $date;
    }

    /**
     * Returns a formatted string.
     * https://momentjs.com/
     *
     * @param  string $format_month default: '%b. ' (short month), '%B ' (long month)
     * @return string "4. Mar 1971"
     */
    public function formatted(
        string $locale = '',
        string $format_y = 'YYYY',
        string $format_m = 'MMM YYYY',
        string $format_d = 'Do MMM YYYY'
        ) : string
    {

        $string = trans('antondate::antondate.no_date');
        $locale = $locale ?: \App::getLocale();

        if ($this->year > 0) {
            $arr = explode('-', $this->toMysqlDate());

            list($y, $m, $d) = $arr;

            $format = self::getDateFormat($this->toMysqlDate());

            switch ($format) {
                case 'Y':
                    $string = Carbon::createFromFormat('Y', static::cleanDate($this->toMysqlDate()))->locale($locale)->isoFormat($format_y);
                    break;
                case 'Y-m':
                    $string = Carbon::createFromFormat('Y-m', static::cleanDate($this->toMysqlDate()))->locale($locale)->isoFormat($format_m);
                    break;
                default:
                    $string = Carbon::createFromFormat('Y-m-d', static::cleanDate($this->toMysqlDate()))->locale($locale)->isoFormat($format_d);
                    break;
            }
        }

        return $string;
    }

    /**
     * Returns a date for storage in a mysql-db
     *
     * @param  string $date 1947 or 1947-02-11
     * @return string date in iso-format or 0000-00-00
     */
    public function toMysqlDate() : string
    {
        $str = self::cleanDate(
            str_pad($this->getYear(),  4, "0", STR_PAD_LEFT) . '-' .
            str_pad($this->getMonth(), 2, "0", STR_PAD_LEFT) . '-' .
            str_pad($this->getDay(),   2, "0", STR_PAD_LEFT)
        );
        return (string) self::addZeros($str);
    }

    /**
     * Returns the string representation of value.
     *
     * @return string
     */
    public function __toString() : string
    {
        $str = '';
        $str .= self::translateCa($this->ca);
        $str .= self::cleanDate(
            str_pad($this->getYear(),  4, "0", STR_PAD_LEFT) . '-' .
            str_pad($this->getMonth(), 2, "0", STR_PAD_LEFT) . '-' .
            str_pad($this->getDay(),   2, "0", STR_PAD_LEFT)
        );

        return (string) $str;
    }

    /**
     * Returns 0 or 1.
     *
     * @return integer
     */
    public function getCa() : int
    {
        return (int) $this->ca;
    }

    /**
     * Returns year
     */
    public function getYear() : int
    {
        return (int) $this->year;
    }

    /**
     * Returns month
     */
    public function getMonth() : int
    {
        return (int) $this->month;
    }

    /**
     * Returns day
     */
    public function getDay() : int
    {
        return (int) $this->day;
    }


    // ************
    // comparators
    // ************

    /**
     * @param  AntonDate $date
     * @param  boolean $strict: $this->ca is evaluated
     * @return boolean
     */
    public function isEqualTo(AntonDate $date, bool $strict = false) : bool
    {
        if ($strict) {
            return ($this->toArray() == $date->toArray());
        }
        return $this->toArray(false) == $date->toArray(false);
    }

    /**
     * @param  AntonDate $date
     * @return boolean
     */
    public function isGreaterThan(AntonDate $date) : bool
    {
        if ($this->toMysqlDate() == '0000-00-00' || $date->toMysqlDate() == '0000-00-00') {
            return true;
        }
        return $this->toInteger() > $date->toInteger();
    }

    /**
     * @param  AntonDate $date
     * @return boolean
     */
    public function isLessThan(AntonDate $date) : bool
    {
        if ($this->toMysqlDate() == '0000-00-00' || $date->toMysqlDate() == '0000-00-00') {
            return true;
        }

        return $this->toInteger() < $date->toInteger();
    }

    // ******************
    // private functions
    // ******************

     /**
     * Create a new Date
     * [year  => JJJJ
     *  month => mm
     *  day => dd
     *  ca => 1]
     * @param object $date new validated AntonDate
     */
    public function __construct(array $date)
    {
        $this->year  = (int) $date['year'];
        $this->month = (int) $date['month'];
        $this->day   = (int) $date['day'];
        $this->ca    = $date['ca'];
        $this->validate();
    }

    /**
     * Validate AntonDate
     * @param  string $value expected to be an AntonDate
     * @throws \InvalidArgumentException
     * @return void
     */
    private function validate()
    {
        if (!AntonDate::isValidString($this->toString())) {
            throw new \InvalidArgumentException(sprintf('AntonDate is not valid: (%s, %s, %s, %s).',
                $this->getYear(), $this->getMonth(), $this->getDay(), $this->ca ? '1' : '0')
            );
        }
    }

    /**
     * determines the date format for Carbons isoFormat()
     *
     * @return string 'Y-m-d', 'Y-m' or'Y'
     */
    private function getDateFormat()
    {
        if ($this->getYear() > 0 && $this->getMonth() == 0 && $this->getDay() == 0) {
            return 'Y';
        }
        if ($this->getYear() > 0 && $this->getMonth() > 0 && $this->getDay() == 0) {
            return 'Y-m';
        }
        if ($this->getYear() > 0 && $this->getMonth() > 0 && $this->getDay() > 0) {
            return 'Y-m-d';
        }

        throw new \Exception("Could not determine a format for AntonDate");
    }

    /**
     * removes "-00" for undefined months and days
     * Converts a MysqlDateString to a AntonDateString
     *
     * @param  string $datestring
     * @return string
     */
    private static function cleanDate($datestring)
    {
        $datestring = trim($datestring);
        $datestring = preg_replace('/(-00$)/', '', $datestring);
        $datestring = preg_replace('/(-00$)/', '', $datestring);

        return $datestring;
    }

    /**
     * AntonDate to MysqlDate (1973 --> 1973-00-00)
     *
     * @param string $datestring [description]
     */
    private static function addZeros($datestring)
    {
        $with_zeros = $datestring;

        if (preg_match('/^\d{1,4}-\d{2}$/', $datestring)) {
            $with_zeros = $datestring .'-00';
        }

        if (preg_match('/^\d{1,4}$/', $datestring)) {
            $with_zeros = $datestring .'-00-00';
        }

        return $with_zeros;
    }

    /**
     * @return string "19710304"
     */
    private function toInteger()
    {
        return (int) str_replace('-', '', $this->toMysqlDate());
    }

    private static function translateCa($ca)
    {
        return ($ca == 1) ? trans('antondate::antondate.ca').' ' : '';
    }

    private static function checkBool($ca)
    {
        if (in_array($ca, ['1', '0', 1, 0, true, false])) {
            return true;
        }
        throw new \InvalidArgumentException('AntonDate is not valid: ' . $ca .' is not boolean.');
    }

    public function formatDate($nullable = false)
    {
        if ($this->toString() !== '0000') {
            $html = $this->formatted();
        } else {
            if (!$nullable) {
                $html = trans('antondate::antondate.no_date');
            } else {
                return '';
            }
        }

        return $html;
    }

    private static function getMonths()
    {
        $months = [];
        for ( $i = 1; $i <= 12; $i++ ) {
            $months[$i] = date_format(date_create('2000-'.str_pad($i, 2, 0, STR_PAD_LEFT).'-01'), 'F');
        }
        return $months;
    }
}
