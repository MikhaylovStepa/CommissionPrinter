<?php

declare(strict_types=1);

namespace Service;

use Model\Transaction;
use Service\BinProvider\BinProviderManager;
use Service\CurrencyRateProvider\CurrencyRateProviderManager;

class CommissionCalculator
{
    const EU_ALPHA_2_LIST = [
        'AT',
        'BE',
        'BG',
        'CY',
        'CZ',
        'DE',
        'DK',
        'EE',
        'ES',
        'FI',
        'FR',
        'GR',
        'HR',
        'HU',
        'IE',
        'IT',
        'LT',
        'LU',
        'LV',
        'MT',
        'NL',
        'PO',
        'PT',
        'RO',
        'SE',
        'SI',
        'SK',
    ];
    const EU_COMMISSION_RATE = 0.01;
    const NON_EU_COMMISSION_RATE = 0.02;

    private $binProviderManager;
    private $currencyRateProviderManager;

    public function __construct(BinProviderManager $binProvider, CurrencyRateProviderManager $currencyRateProvider)
    {
        $this->binProviderManager = $binProvider;
        $this->currencyRateProviderManager = $currencyRateProvider;
    }

    public function calculate(
        Transaction $transaction,
        string $binProvider,
        string $currencyProvider
    ): float
    {
        $countryAlpha2 = $this->binProviderManager->getCountryAlpha2($transaction->getBin(), $binProvider);
        $commissionRate = in_array($countryAlpha2, self::EU_ALPHA_2_LIST)
            ? self::EU_COMMISSION_RATE
            : self::NON_EU_COMMISSION_RATE;
        $currencyRate = $this->currencyRateProviderManager->getRate($transaction->getCurrency(), $currencyProvider);
        if ($currencyRate <= 0) {
            throw new \InvalidArgumentException('Currency rate must be greater than zero!');
        }

        return ceil($transaction->getAmount() / $currencyRate * $commissionRate * 100) / 100;
    }
}