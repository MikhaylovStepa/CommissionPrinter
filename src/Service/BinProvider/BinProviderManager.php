<?php

declare(strict_types=1);

namespace Service\BinProvider;

use Exception\BinProviderException;

class BinProviderManager
{
    /**
     * @var BinProviderInterface[]
     */
    private $providers = [];

    public function addProvider(BinProviderInterface $provider): self
    {
        $this->providers[$provider->getKey()] = $provider;

        return $this;
    }

    /**
     * @param string $bin
     * @param string $providerKey
     * @return string
     * @throws BinProviderException
     */
    public function getCountryAlpha2(string $bin, string $providerKey): string
    {
        if (!isset($this->providers[$providerKey])) {
            throw new BinProviderException("Bin provider was not found!");
        }

        return $this->providers[$providerKey]->getCountryAlpha2($bin);
    }
}