<?php

declare(strict_types=1);

namespace Service;

use Exception\BinProviderException;
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
use Service\BinProvider\LookUpBinListProvider;

class LookUpBinProviderTest extends TestCase
{
    const BIN_RESPONSE = <<<EOL
    {
        "country": {
             "alpha2": "DK"
        }
    }
EOL;
    const EXPECTED_ALPHA2 = "DK";
    const BIN = '1111';

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
            ->willReturn(self::BIN_RESPONSE);
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

    /**
     * @return void
     */
    public function testWithExistedBin(): void
    {
        $provider = new LookUpBinListProvider($this->serializer, $this->guzzleClient);
        $actualRate = $provider->getCountryAlpha2(self::BIN);
        $this->assertEquals(self::EXPECTED_ALPHA2, $actualRate);
    }

    public function testWithNotExistedBin(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $serializer = $this->getMockBuilder(SerializerInterface::class)->getMock();
        $serializer->expects($this->once())
            ->method('deserialize')
            ->withAnyParameters()
            ->willReturn(null);

        $provider = new LookUpBinListProvider($serializer, $this->guzzleClient);
        $provider->getCountryAlpha2(self::BIN);
    }

    public function testGuzzleException(): void
    {
        $this->expectException(BinProviderException::class);
        /** @var MockObject|GuzzleException $exception */
        $exception = $this->getMockBuilder(GuzzleException::class)->getMock();
        $this->guzzleClient
            ->expects($this->once())
            ->method('request')
            ->withAnyParameters()
            ->willThrowException($exception);
        $provider = new LookUpBinListProvider($this->serializer, $this->guzzleClient);
        $provider->getCountryAlpha2(self::BIN);
    }
}