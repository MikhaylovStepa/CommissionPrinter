<?php

declare(strict_types=1);

namespace Service;

use Model\Transaction;
use Service\BinProvider\BinProviderInterface;
use Service\CurrencyRateProvider\CurrencyRateProviderInterface;

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

    private $binProvider;
    private $currencyRateProvider;

    /**
     * @param BinProviderInterface $binProvider
     * @param CurrencyRateProviderInterface $currencyRateProvider
     */
    public function __construct(BinProviderInterface $binProvider, CurrencyRateProviderInterface $currencyRateProvider)
    {
        $this->binProvider = $binProvider;
        $this->currencyRateProvider = $currencyRateProvider;
    }

    public function calculate(Transaction $transaction): float
    {
        $countryAlpha2 = $this->binProvider->getCountryAlpha2($transaction->getBin());
        $commissionRate = in_array($countryAlpha2, self::EU_ALPHA_2_LIST)
            ? self::EU_COMMISSION_RATE
            : self::NON_EU_COMMISSION_RATE;
        $currencyRate = $this->currencyRateProvider->getRate($transaction->getCurrency());
        if ($currencyRate <= 0) {
            throw new \InvalidArgumentException('Currency rate must be greater than zero!');
        }

        return ceil($transaction->getAmount() / $currencyRate * $commissionRate * 100) / 100;
    }
}