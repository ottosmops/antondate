<?php
namespace Ottosmops\Antondate\ValueObjects;

use Ottosmops\Antondate\ValueObjects\ValueObjectInterface;
use Ottosmops\Antondate\ValueObjects\AntonDate;

/**
 * AntonDateInterval consists of two AntonDates.
 */
final class AntonDateInterval implements ValueObjectInterface
{
    //const DATE_PATTERN = '((?:ca. )?(?:\d{3,4}(?:-\d{2})?(?:-\d{2})?))';
    const DATE_INTERVAL_PATTERN = AntonDate::ANTON_DATE_PATTERN .'[/-]'.AntonDate::ANTON_DATE_PATTERN;
    /* @var Antondate */
    private $date_start;

    /* @var Antondate */
    private $date_end;

    /**
     * [createFromString description]
     * @param  string $string    eg. 'ca. 1984/1986-12-03'
     * @param  string $separator '/' by default
     * @return obj               AntonDateInterval
     */
    public static function createFromString($string = '', $separator = '/')
    {
        $dateIntervalString = trim($string); // work with $datestring

        if (!preg_match('#' . self::DATE_INTERVAL_PATTERN . '#', $dateIntervalString)) {
            throw new \InvalidArgumentException("Could not parse string to AntonDate (the regex does not match) $string");
        }

        // get separator
        if (preg_match('#\d{3,4}-\d{3,4}#', $dateIntervalString)) {
            $separator = '-';
        }

        list($date_start, $date_end) =  explode($separator, $dateIntervalString);

        if (AntonDate::isValidString($date_start)) {
            $AntonDateStart = AntonDate::createFromString($date_start);
        } else {
            throw new \InvalidArgumentException("Could not parse string to AntonDate (could not initiate a AntonStartDate) $string");
        }

        if (isset($date_end)) {
            if (AntonDate::isValidString($date_end)) {
                $AntonDateEnd = AntonDate::createFromString($date_end);
            } else {
                throw new \InvalidArgumentException("Could not parse string to AntonDate (could not initiate a AntonEndDate) $string");
            }
        } else {
            $AntonDateEnd = AntonDate::createFromString('0000-00-00');
        }

        return new static($AntonDateStart, $AntonDateEnd);
    }

    /**
     * Compose AntonDateInterval from to AntonDateStrings
     *
     * @param string $date_start
     * @param boolean $date_start_ca
     * @param string $date_end
     * @param boolean $date_end_ca
     * @return self
     */
    public static function compose(string $date_start, bool $date_start_ca, string $date_end, bool $date_end_ca) : self
    {
        return new static(
            AntonDate::createFromString($date_start, $date_start_ca),
            AntonDate::createFromString($date_end, $date_end_ca),
        );
    }

    /**
     * isValid checks if a given dateInterval is valid
     * @param  string  $value     dateInterval
     * @param  string  $separator '/' by default
     * @return boolean
     */
    public static function isValidString($value, $separator = '/')
    {
        if (!preg_match('#' . self::DATE_INTERVAL_PATTERN . '#', $value)) {
            return false;
        }
        if (preg_match('#\d{3,4}-\d{3,4}#', $value)) {
            list($date_start, $date_end) =  explode('-', $value);
        } else {
            list($date_start, $date_end) =  explode('/', $value);
        }

        if (AntonDate::isValidString($date_start)) {
            $antonDateStart = AntonDate::createFromString($date_start);
        } else {
            return false;
        }

        if (isset($date_end)) {
            if (AntonDate::isValidString($date_end)) {
                $antonDateEnd = AntonDate::createFromString($date_end);
            } else {
                return false;
            }
        } else {
            $date_end = '0000-00-00';
        }

        $isValid = $antonDateStart->isLessThan($antonDateEnd)
                || $antonDateStart->isEqualTo($antonDateEnd)
                || (AntonDate::isValidString($date_start) && $date_end == '0000-00-00');
        return $isValid;
    }

    public function toString()
    {
        return (string) $this->__toString();
    }

    public function __toString()
    {
        $str = $this->AntonDateStart->toString();

        if ($this->AntonDateEnd->toMysqlDate() !== '0000-00-00') {
            $str.= '/' . $this->AntonDateEnd->toString();
        }

        return $str;
    }

    public function mysqlDateArray($option = '')
    {
        if ($option == 'nullable') {
            return array_merge($this->dateInterval, ['date_end'=> null, 'date_end_ca'=> 0]);
        }
        return $this->dateInterval;
    }

    public function dateStartCa()
    {
        return $this->AntonDateStart->getCa();
    }

    public function dateEndCa()
    {
        return $this->AntonDateEnd->getCa();
    }

    public function mysqlDateStart()
    {
        return $this->AntonDateStart->toMysqlDate();
    }

    public function mysqlDateEnd($option = '')
    {
        if (($option == 'nullable') && ($this->dateInterval['date_end'] == '0000-00-00')) {
            return null;
        }
        return $this->AntonDateEnd->toMysqlDate();
    }

    public function __construct(AntonDate $AntonDateStart, AntonDate $AntonDateEnd)
    {
        $this->AntonDateStart = $AntonDateStart;
        $this->AntonDateEnd = $AntonDateEnd;
    }

    public function toArray()
    {
         return [
             'date_start' => $this->AntonDateStart->toMysqlDate(),
             'date_start_ca' => $this->AntonDateStart->getCa(),
             'date_end' => $this->AntonDateEnd->toMysqlDate(),
             'date_end_ca' => $this->AntonDateEnd->getCa(),
         ];
    }

    /**
     * Returns a rendered date (html)
     *
     * @param boolean $only_year
     * @param boolean $nullable returns an empty string if the date is null/0 etc.
     * @return string
     */
    public function renderDate(bool $only_year = false, bool $nullable = false)
    {
        $date_start_ca = 0 == $this->AntonDateStart->getCa() ? '' : trans('antondate::antondate.ca') . ' ';
        $date_end_ca = 0 == $this->AntonDateEnd->getCa() ? '' : trans('antondate::antondate.ca') . ' ';

        if ($only_year) {
            if ($nullable) {
                $date_start = $this->AntonDateStart->getYear() > 0 ? $this->AntonDateStart->getYear() : '';
                $date_end = $this->AntonDateEnd->getYear() > 0 ? $this->AntonDateEnd->getYear() : '';
            } else {
                $date_start = $this->AntonDateStart->getYear() > 0
                            ? $this->AntonDateStart->getYear()
                            : trans('antondate::antondate.no_date');
                $date_end = $this->AntonDateEnd->getYear() > 0
                          ? $this->AntonDateEnd->getYear() :
                          trans('antondate::antondate.no_date');
            }
        } else {
            $date_start = $this->AntonDateStart->formatDate($nullable);
            $date_end = $this->AntonDateEnd->formatDate($nullable);
        }

        $html = '';
        $html = $date_start_ca . $date_start;

        if ($this->AntonDateStart->toMysqlDate() != $this->AntonDateEnd->toMysqlDate() || $date_start_ca != $date_end_ca) {
            $html .= ' – ' . $date_end_ca . $date_end;
        }

        $html = str_replace('1er', '1<sup>er</sup>', $html);
        $html = str_replace('juil.', 'juill.', $html);

        return trim($html);
    }
}
