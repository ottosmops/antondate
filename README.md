# Antondate

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Tests](https://github.com/ottosmops/antondate/actions/workflows/run-tests.yml/badge.svg)](https://github.com/ottosmops/antondate/actions/workflows/run-tests.yml)

This is where your description should go. Take a look at [contributing.md](contributing.md) to see a to do list.

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




## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## License

license. Please see the [license file](LICENSE.md) for more information.

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
