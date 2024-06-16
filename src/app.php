<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializerBuilder;
use Service\BinProvider\BinProviderManager;
use Service\BinProvider\LookUpBinListProvider;
use Service\CommissionCalculator;
use Service\CurrencyRateProvider\CurrencyRateProviderManager;
use Service\CurrencyRateProvider\ExchangeRatesApiProvider;
use Service\TransactionFileParser;

$serializer = SerializerBuilder::create()
    ->setPropertyNamingStrategy(new SerializedNameAnnotationStrategy(new IdenticalPropertyNamingStrategy()))
    ->build();
$guzzleClient = new Client();
$transactionFileReader = new TransactionFileParser($serializer);
$transactions = $transactionFileReader->parse($argv[1]);
$lookUpBinListProvider = new LookUpBinListProvider($serializer, $guzzleClient);
$exchangeRatesApiProvider = new ExchangeRatesApiProvider($serializer, $guzzleClient);
$binProviderKey = $argv[2] ?? $lookUpBinListProvider->getKey();
$currencyProviderKey = $argv[3] ?? $exchangeRatesApiProvider->getKey();
$binProviderManager = new BinProviderManager();
$binProviderManager->addProvider($lookUpBinListProvider);
$currencyRateProviderManager = new CurrencyRateProviderManager();
$currencyRateProviderManager->addProvider($exchangeRatesApiProvider);

$commissionCalculator = new CommissionCalculator($binProviderManager, $currencyRateProviderManager);

foreach ($transactions as $transaction) {
    try {
        $commission = $commissionCalculator->calculate($transaction, $binProviderKey, $currencyProviderKey);
        print($commission . PHP_EOL);
    } catch (Exception $exception) {
        print($exception->getMessage() . PHP_EOL);
    }
}