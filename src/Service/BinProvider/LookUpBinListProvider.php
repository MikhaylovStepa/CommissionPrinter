<?php

declare(strict_types=1);

namespace Service\BinProvider;

use Exception\BinProviderException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JMS\Serializer\SerializerInterface;
use Model\BinProvider\LookUpBinListProvider\Result;

class LookUpBinListProvider implements BinProviderInterface
{
    const URL = 'https://lookup.binlist.net/';

    private $serializer;
    private $guzzleClient;

    public function __construct(SerializerInterface $serializer, Client $guzzleClient)
    {
        $this->serializer = $serializer;
        $this->guzzleClient = $guzzleClient;
    }

    function getCountryAlpha2(string $bin): string
    {
        try {
            $response = $this->guzzleClient->request('GET', self::URL . $bin);
            $contents = $response->getBody()->getContents();
            /** @var Result $result */
            $result = $this->serializer->deserialize($contents, Result::class, 'json');

            if (
                $result === null
                || $result->getCountry() === null
                || $result->getCountry()->getAlpha2() === null
            ) {
                throw new \InvalidArgumentException('Can\'t get country alpha2 by BIN');
            }

            return $result->getCountry()->getAlpha2();
        } catch (GuzzleException $guzzleException) {
            throw new BinProviderException('LookUpBinListProvider is currently unavailable');
        }
    }

    function getKey(): string
    {
        return 'LookUpBinListProvider';
    }
}