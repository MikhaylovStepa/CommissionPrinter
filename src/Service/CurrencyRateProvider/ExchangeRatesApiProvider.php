<?php

declare(strict_types=1);

namespace Service\CurrencyRateProvider;

use Exception\CurrencyRateProviderException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JMS\Serializer\SerializerInterface;
use Model\CurrencyRateProvider\ExchangeRatesApi\Result;

class ExchangeRatesApiProvider implements CurrencyRateProviderInterface
{
    const URL = 'https://open.er-api.com/v6/latest/EUR';
    const EUR = "EUR";
    const STATUS_OK = 200;

    private $serializer;
    private $guzzleClient;

    public function __construct(SerializerInterface $serializer, Client $guzzleClient)
    {
        $this->serializer = $serializer;
        $this->guzzleClient = $guzzleClient;
    }

    function getRate(string $currency): float
    {
        try {
            if ($currency === self::EUR) {
                return 1;
            }
            $response = $this->guzzleClient->request('GET', self::URL);
            if ($response->getStatusCode() !== self::STATUS_OK) {
                throw new CurrencyRateProviderException('ExchangeRatesApiProvider is currently unavailable');
            }
            $contents = $response->getBody()->getContents();
            /** @var Result $result */
            $result = $this->serializer->deserialize($contents, Result::class, 'json');
            if (
                $result === null
                || empty($result->getRates())
                || !isset($result->getRates()[$currency])
            ) {
                throw new \InvalidArgumentException('ExchangeRatesApiProvider returns no data');
            }

            return $result->getRates()[$currency];
        } catch (GuzzleException $guzzleException) {
            throw new CurrencyRateProviderException('ExchangeRatesApiProvider is currently unavailable');
        }
    }

    function getKey(): string
    {
        return 'ExchangeRatesApiProvider';
    }
}