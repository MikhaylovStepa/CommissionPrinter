<?php

declare(strict_types=1);

namespace Model\CurrencyRateProvider\ExchangeRatesApi;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

class Result
{
    /**
     * @var array
     * @Type("array")
     * @SerializedName("rates")
     */
    private $rates = [];

    /**
     * @return array
     */
    public function getRates(): array
    {
        return $this->rates;
    }

    /**
     * @param array $rates
     */
    public function setRates(array $rates): void
    {
        $this->rates = $rates;
    }
}