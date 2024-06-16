<?php

declare(strict_types=1);

namespace Service\CurrencyRateProvider;

use Exception\CurrencyRateProviderException;

class CurrencyRateProviderManager
{
    /**
     * @var CurrencyRateProviderInterface[]
     */
    private $providers = [];

    public function addProvider(CurrencyRateProviderInterface $provider): self
    {
        $this->providers[$provider->getKey()] = $provider;

        return $this;
    }

    /**
     * @param string $currency
     * @param string $providerKey
     * @return float
     * @throws CurrencyRateProviderException
     */
    public function getRate(string $currency, string $providerKey): float
    {
        if (!isset($this->providers[$providerKey])) {
            throw new CurrencyRateProviderException("Currency provider was not found!");
        }

        return $this->providers[$providerKey]->getRate($currency);
    }
}