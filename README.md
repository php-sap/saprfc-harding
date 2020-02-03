# PHP/SAP implementation for Piers Hardings sapnwrfc module

[![License: MIT][license-mit]](LICENSE)
[![Build Status][travis-badge]][travis-ci]
[![Maintainability][maintainability-badge]][maintainability]
[![Test Coverage][coverage-badge]][coverage]

This repository implements the [PHP/SAP][phpsap] interface for [Piers Hardings `sapnwrfc` PHP module][harding].

## Usage

```sh
composer require php-sap/saprfc-harding
```

```php
<?php
//Include the composer autoloader ...
require_once 'vendor/autoload.php';
//... and add the namespaces of the classes used.
use phpsap\classes\Config\ConfigTypeA;
use phpsap\DateTime\SapDateTime;
use phpsap\saprfc\SapRfc;
/**
 * Create an instance of the SAP remote function using its
 * name, input parameters, and connection configuration.
 *
 * The imaginary SAP remote function requires a
 * date as input and will return a date as output.
 *
 * In this case the configuration array is defined manually.
 */
$result = (new SapRfc(
  'MY_COOL_SAP_REMOTE_FUNCTION',
  [
      'IV_DATE' => (new DateTime('2019-12-31'))
                   ->format(SapDateTime::SAP_DATE)
  ],
  new ConfigTypeA([
      ConfigTypeA::JSON_ASHOST => 'sap.example.com',
      ConfigTypeA::JSON_SYSNR  => '999',
      ConfigTypeA::JSON_CLIENT => '001',
      ConfigTypeA::JSON_USER   => 'username',
      ConfigTypeA::JSON_PASSWD => 'password'
  ])
))->invoke();
//The output array contains a DateTime object.
echo $result['OV_DATE']->format('Y-m-d') . PHP_EOL;
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
