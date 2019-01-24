# PHP/SAP implementation for Piers Hardings sapnwrfc module

[![License: MIT][license-mit]](LICENSE)
[![Build Status][travis-badge]][travis-ci]
[![Maintainability][maintainability-badge]][maintainability]
[![Test Coverage][coverage-badge]][coverage]

This repository implements the [PHP/SAP][phpsap] interface for [Piers Hardings `sapnwrfc` PHP module][harding].

## Usage

```sh
composer require php-sap/saprfc-harding:^1.0
```

```php
<?php
use phpsap\saprfc\SapRfcConfigA;
use phpsap\saprfc\SapRfcConnection;

$result = (new SapRfcConnection(new SapRfcConfigA([
  'ashost' => 'sap.example.com',
  'sysnr' => '001',
  'client' => '002',
  'user' => 'username',
  'passwd' => 'password'
])))
    ->prepareFunction('MY_COOL_SAP_REMOTE_FUNCTION')
    ->invoke(['INPUT_PARAM' => 'value']);
```

For further documentation, please read the documentation on [PHP/SAP][phpsap]!

[phpsap]: https://php-sap.github.io
[harding]: https://github.com/piersharding/php-sapnwrfc "SAP RFC Connector using the SAP NW RFC SDK for PHP"
[license-mit]: https://img.shields.io/badge/license-MIT-blue.svg
[travis-badge]: https://travis-ci.org/php-sap/saprfc-harding.svg?branch=master
[travis-ci]: https://travis-ci.org/php-sap/saprfc-harding
[maintainability-badge]: https://api.codeclimate.com/v1/badges/81cbf146565bc4d1af4f/maintainability
[maintainability]: https://codeclimate.com/github/php-sap/saprfc-harding/maintainability
[coverage-badge]: https://api.codeclimate.com/v1/badges/81cbf146565bc4d1af4f/test_coverage
[coverage]: https://codeclimate.com/github/php-sap/saprfc-harding/test_coverage
