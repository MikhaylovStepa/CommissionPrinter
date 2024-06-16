<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializerBuilder;
use Service\BinProvider\LookUpBinListProvider;
use Service\CommissionCalculator;
use Service\CurrencyRateProvider\ExchangeRatesApiProvider;
use Service\TransactionFileParser;

$serializer = SerializerBuilder::create()
    ->setPropertyNamingStrategy(new SerializedNameAnnotationStrategy(new IdenticalPropertyNamingStrategy()))
    ->build();
$guzzleClient = new Client();
$transactionFileReader = new TransactionFileParser($serializer);
$transactions = $transactionFileReader->parse($argv[1]);

$commissionCalculator = new CommissionCalculator(
    new LookUpBinListProvider($serializer, $guzzleClient),
    new ExchangeRatesApiProvider($serializer, $guzzleClient));

foreach ($transactions as $transaction) {
    try {
        $commission = $commissionCalculator->calculate($transaction);
        print($commission . PHP_EOL);
    } catch (Exception $exception) {
        print($exception->getMessage() . PHP_EOL);
    }
}