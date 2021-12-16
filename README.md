# Antondate

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Tests](https://github.com/ottosmops/antondate/actions/workflows/run-tests.yml/badge.svg)](https://github.com/ottosmops/antondate/actions/workflows/run-tests.yml)

Using Dates in Anton ([anton.ch](https://www.anton.ch).

## Installation

Via Composer

``` bash
$ composer require ottosmops/antondate
```

Add `date_start`, `date_start_ca`, `date_end`, `date_end_ca` to the database table.

Add the casts to the model:

```php
protected $casts = [
    'anton_date_interval' => AntonDateIntervalCast::class,
    'anton_date_start' => AntonDateStartCast::class,
    'anton_date_end' => AntonDateEndCast::class,
];
```

## Usage

The package covers two ValueObjects: AntonDate, AntonDateInterval (consisting of two AntonDates).

### Create an AntonDate

```php
AntonDate::createFromString('1995-03-01', 1) : AntonDate
// ca. 1995-03-01

AntonDate::guessFromString('4. Mai 1905') : AntonDate
// 1905-05-04

AntonDate::compose(1973, 12, 3, 1) : AntonDate
// ca. 1973-12-03

AntonDate::today() : AntonDate
```

### Validate
```php
AntonDate::isValidString('1997-13-01'); // false
AntonDate::isValidString('ca. 1997-11-01'); // true
```

### Get
```php
$antondate->toString();
$antondate->toArray();
$antondate->formatted();
$antondate->toMysqlDate();
$antondate->getCa();
$antondate->getYear();
$antondate->getMonth();
$antondate->getDay();
```

### Compare
```
$antondate->isEqualTo($antondate2, true); // compare with ca
$antondate->isEqualTo($antondate2); // compare without ca
$antondate->isGreaterThan($antondate2);
$antondate->isLessThan($antondate2);
```

### Rule
There is also a rule. Which you can use for validation: `AntonDateRule::class`.


## License

MIT. Please see the [license file](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/ottosmops/antondate.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/ottosmops/antondate.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/ottosmops/antondate/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/ottosmops/antondate
[link-downloads]: https://packagist.org/packages/ottosmops/antondate
[link-travis]: https://travis-ci.org/ottosmops/antondate
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/ottosmops
[link-contributors]: ../../contributors
