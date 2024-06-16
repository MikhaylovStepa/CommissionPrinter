<?php

namespace Service\CurrencyRateProvider;

use Exception\CurrencyRateProviderException;

interface CurrencyRateProviderInterface
{
    /**
     * @param string $currency
     * @return float
     * @throws CurrencyRateProviderException
     */
    function getRate(string $currency): float;
}