<?php

namespace Ottosmops\Antondate;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Ottosmops\Antondate\ValueObjects\AntonDate;

class DateHelper
{
    /**
     * Parse a date and return a standardized string (YYYY-mm-dd)
     *
     * @param string $value
     * @return string
     */
    public static function parseDateString(string $value) : string
    {
        $value = trim($value);
        if (preg_match('/\d+\.\d+\.\d{4}/', $value)) {
            $value = date('Y-m-d', strtotime($value));
        }

        if (preg_match('/(\d+)\.\s?([A-Za-zä]+)\.?\s+(\d{4})/', $value, $matches)) {
            $day    = trim($matches[1]);
            for ($i=1; $i<13; $i++) {
                $months[$i] = Carbon::parse('2012-'.$i.'-5')->monthName;
            }

            $month  = 0;
            foreach ($months as $k => $v) {
                if (Str::startsWith($v, $matches[2])) {
                    $month = $k;
                }
            }
            $year  = $matches[3];
            $value = date('Y-m-d', strtotime("$day-$month-$year"));
        }
        return $value;
    }

    /**
     * converts date information to string
     * @param   string  $date_start
     * @param   bool    $date_start_ca
     * @param   string  $date_end
     * @param   bool    $date_end_ca
     * @return  string
     *       'date_start_ca' => 0,
     *       'date_end'       => '1950-10-01',
     *       'date_end_ca'    => 1)
     */
    public static function renderDate(
        string $date_start,
        bool $date_start_ca,
        string $date_end,
        bool $date_end_ca, bool
        $only_year = false,
        bool $nullable = false
        ) : string
    {
        $ca = trans('messages.ca') == 'messages.ca' ? 'ca. ' : trans('messages.ca').' ';

        $date_start_ca = 0 == $date_start_ca ? '' : $ca;
        $date_end_ca = 0 == $date_end_ca ? '' : $ca;

        if ($only_year) {
            $date_start = substr($date_start, 0, 4);
            $date_end = substr($date_end, 0, 4);
        }

        $html = '';
        $html = $date_start_ca . self::formatDate($date_start, $nullable);

        if ($date_start != $date_end || $date_start_ca != $date_end_ca) {
            $html .= ' – ' . $date_end_ca . self::formatDate($date_end);
        }

        $html = str_replace('1er', '1<sup>er</sup>', $html);
        $html = str_replace('juil.', 'juill.', $html);

        return $html;
    }

    public static function formatDate(string $string, bool $nullable = false): ?string
    {
        if ('0000-00-00 00:00:00' != $string
             && '-0001-11-30 00:00:00' != $string
             && null != $string
             && '0000-00-00' != $string
             && '-0001' != $string) {
            //$html = $string .' :: ';
            $html = AntonDate::compose($string)->formatted();
        } else {
            if (!$nullable) {
                $html = trans('messages.no_date');
            } else {
                return null;
            }
        }
        return $html;
    }

    /**
     * only for calculation
     * Do not save the date after conversion.
     * @param  string $date Anont
     * @return object DateTime
     */
    public static function antonDate2carbonDate($date)
    {
        $aDate = explode('-', self::cleanDate($date));
        $aDate += [1, 1, 1];
        list($y, $m, $d) = $aDate;
        $y = '0000' == $y ? '0001' : $y;
        $carbonDate = \Carbon\Carbon::create($y, $m, $d, 0, 0, 0);
        return $carbonDate;
    }

    /**
     * [year description]
     * @param  string $date 2003-01-03
     * @return ?string       2003
     */
    public static function year(string $date) : ?string
    {
        if (self::checkIsoDate($date)) {
            return date("Y", strtotime($date));
        }
        return null;
    }

    public static function cleanDate(string $datestring): string
    {
        $datestring = preg_replace('/(-00$)/', '', $datestring);
        $datestring = preg_replace('/(-00$)/', '', $datestring);
        return $datestring;
    }

    public static function composeDate(int $year = 0, int $month = 0, int $day = 0): string
    {
        $date = '';

        $year = ($year >= 1 && $year <= 2100) ? $year : 0;
        $date .= str_pad((string) $year, 4, "0", STR_PAD_LEFT);

        $month = ($month >= 1 && $month <= 12) ? $month : 0;
        $date .= '-' . str_pad((string) $month, 2, "0", STR_PAD_LEFT);

        $day = ($day >= 1 && $day <= 31) ? $day : 0;
        $date .= '-' . str_pad((string) $day, 2, "0", STR_PAD_LEFT);

        return $date;
    }

    /**
     * Validiert ein ISO Datum
     *
     * Gibt true zurück, wenn datum folgendem Muster folgt:
     * 2004-02-01, 20040201, 00530201
     *
     * Beispiel:
     * checkIsoDate('2011-09-11')
     *
     * @author  ak
     * @version 1.0 | 2014-01-15
     *
     * @param   string  $isoDate zu ueberpruefendes ISO Datum
     * @return  bool
     *
     */
    public static function checkIsoDate(string $isoDate) : bool
    {
        $return = false;
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $isoDate, $parts)) {
            if (checkdate((int) $parts[2], (int) $parts[3], (int) $parts[1])) {
                $return = true;
            }
        }
        return $return;
    }
}
