<?php

declare(strict_types=1);

namespace tests\Service;

use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Model\Transaction;
use PHPUnit\Framework\TestCase;
use Service\TransactionFileParser;

class TransactionFileParserTest extends TestCase
{
    const CORRECT_FORMAT_PATH = 'tests/Resource/correct_data.txt';
    const INCORRECT_FORMAT_PATH = 'tests/Resource/failed_data.txt';

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function setUp(): void
    {
        parent::setUp();
        $this->serializer = SerializerBuilder::create()
            ->setPropertyNamingStrategy(new SerializedNameAnnotationStrategy(new IdenticalPropertyNamingStrategy()))
            ->build();
    }

    public function testCorrectParsing(): void
    {
        $transactionFileParser = new TransactionFileParser($this->serializer);
        $actualTransactions = $transactionFileParser->parse(self::CORRECT_FORMAT_PATH);
        $expectedTransactions = $this->getExpectedTransactions();
        $this->assertIsArray($actualTransactions);
        $this->assertSameSize($expectedTransactions, $actualTransactions);
        for ($i = 0; $i < count($expectedTransactions); $i++) {
            $this->assertEquals($expectedTransactions[$i], $actualTransactions[$i]);
        }
    }

    public function testFailedParsing(): void
    {
        $this->expectException(\RuntimeException::class);
        $transactionFileParser = new TransactionFileParser($this->serializer);
        $transactionFileParser->parse(self::INCORRECT_FORMAT_PATH);
    }

    private function getExpectedTransactions(): array
    {
        return [
            new Transaction('45717360', '100.00', 'EUR'),
            new Transaction('516793', '50.00', 'USD'),
            new Transaction('45417360', '10000.00', 'JPY'),
            new Transaction('41417360', '130.00', 'USD'),
            new Transaction('4745030', '2000.00', 'GBP'),
        ];
    }
}