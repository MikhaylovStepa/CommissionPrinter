## Pre-Installation requirements
 * php 7.2
 * install composer locally or global

## Installation

Better way is using local composer.phar file:
```bash
$ php composer.phar install 
```

## Running the code
Open root of the project in terminal and execute the command:
```bash
$ php src/app.php path_to_file bin_provider_key currency_exchange_provider_key
```
where 
* path_to_file - Path to file with transactions
* bin_provider_key - Bin provider key registered in BinProviderManager
* currency_exchange_provider_key - Currency exchange provider key registered in CurrencyExchangeProviderManager

## Running Unit Tests
Open root of the project in terminal and execute the command:
```bash
$ vendor/bin/phpunit
```