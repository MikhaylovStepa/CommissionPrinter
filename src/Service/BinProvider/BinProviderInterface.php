<?php

namespace Service\BinProvider;

use Exception\BinProviderException;

interface BinProviderInterface
{
    /**
     * @param string $bin
     * @return string
     * @throws BinProviderException
     */
    function getCountryAlpha2(string $bin): string;

    function getKey(): string;
}