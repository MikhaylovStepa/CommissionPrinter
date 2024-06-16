<?php

declare(strict_types=1);

namespace Service;

use Exception\CurrencyRateProviderException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Service\CurrencyRateProvider\ExchangeRatesApiProvider;

class ExchangeRatesApiProviderTest extends TestCase
{
    const RATES = <<<EOL
   {
       "rates": {
            "EUR": 1,
            "USD": 1.03
       }
   }
EOL;
    const NOT_EXISTED_CURRENCY = 'ABC';
    const FOREIGN_CURRENCY = 'USD';

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var MockObject|Client
     */
    private $guzzleClient;

    public function setUp(): void
    {
        parent::setUp();
        $this->serializer = SerializerBuilder::create()
            ->setPropertyNamingStrategy(new SerializedNameAnnotationStrategy(new IdenticalPropertyNamingStrategy()))
            ->build();
        /** @var MockObject|StreamInterface $body */
        $body = $this->createMock(StreamInterface::class);
        $body->expects($this->any())
            ->method('getContents')
            ->willReturn(self::RATES);
        /** @var MockObject|ResponseInterface $response */
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->any())
            ->method('getBody')
            ->willReturn($body);
        $response->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(200);
        /** @var MockObject|Client $guzzleClient */
        $guzzleClient = $this->createMock(Client::class);
        $guzzleClient->expects($this->any())
            ->method('request')
            ->withAnyParameters()
            ->willReturn($response);
        $this->guzzleClient = $guzzleClient;
    }

    public function correctDataSet(): array
    {
        return [
            'EUR currency' =>
                [
                    'EUR',
                    1,
                ],
            'USD currency' =>
                [
                    'USD',
                    1.03,
                ],
        ];
    }

    /**
     * @dataProvider correctDataSet
     * @param $currency
     * @param $expectedRate
     * @return void
     */
    public function testWithExistedCurrency($currency, $expectedRate): void
    {
        $provider = new ExchangeRatesApiProvider($this->serializer, $this->guzzleClient);
        $actualRate = $provider->getRate($currency);
        $this->assertEquals($expectedRate, $actualRate);
    }

    public function testWithNotExistedCurrency(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $provider = new ExchangeRatesApiProvider($this->serializer, $this->guzzleClient);
        $provider->getRate(self::NOT_EXISTED_CURRENCY);
    }

    public function testGuzzleException(): void
    {
        $this->expectException(CurrencyRateProviderException::class);
        /** @var MockObject|GuzzleException $exception */
        $exception = $this->createMock(GuzzleException::class);
        $this->guzzleClient
            ->expects($this->once())
            ->method('request')->withAnyParameters()->willThrowException($exception);
        $provider = new ExchangeRatesApiProvider($this->serializer, $this->guzzleClient);
        $provider->getRate(self::FOREIGN_CURRENCY);
    }
}