<?php

declare(strict_types=1);

namespace tests\Service;

use Model\Transaction;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Service\BinProvider\BinProviderInterface;
use Service\CommissionCalculator;
use Service\CurrencyRateProvider\CurrencyRateProviderInterface;

class CommissionCalculatorTest extends TestCase
{
    /**
     * @var MockObject|BinProviderInterface $mockedBinProvider
     */
    private $mockedBinProvider;
    /**
     * @var MockObject|CurrencyRateProviderInterface $mockedCurrencyRateProvider
     */
    private $mockedCurrencyRateProvider;

    public function setUp(): void
    {
        parent::setUp();
        /** @var MockObject|BinProviderInterface $mockedBinProvider */
        $this->mockedBinProvider = $this->createMock(BinProviderInterface::class);
        $this->mockedBinProvider
            ->expects($this->once())
            ->method('getCountryAlpha2')
            ->willReturnCallback(function () {
                $args = func_get_args();
                if ($args[0] === '1111') {
                    return 'AT';
                } else if ($args[0] === '2222') {
                    return 'US';
                }

                return null;
            });

        /** @var MockObject|CurrencyRateProviderInterface $mockedCurrencyRateProvider */
        $this->mockedCurrencyRateProvider = $this->createMock(CurrencyRateProviderInterface::class);
        $this->mockedCurrencyRateProvider
            ->expects($this->once())
            ->method('getRate')
            ->willReturnCallback(function () {
                $args = func_get_args();
                if ($args[0] === 'USD') {
                    return 1.2;
                } else if ($args[0] === 'JPY') {
                    return 0.83;
                } else if ($args[0] === 'EUR') {
                    return 1;
                }

                return 0;
            });
    }

    public function correctDataSet(): array
    {
        return [
            'EU bin, EUR currency' =>
                [
                    new Transaction('1111', 1234.5, 'EUR'),
                    12.35,
                ],
            'EU bin, not currency' =>
                [
                    new Transaction('1111', 23456, 'USD'), // currency rate 1.2
                    195.47,
                ],
            'Not EU bin, EUR currency' =>
                [
                    new Transaction('2222', 3456.7, 'EUR'),
                    69.14,
                ],
            'Not EU bin, not EUR currency' =>
                [
                    new Transaction('2222', 45678, 'JPY'), // currency rate 0.83
                    1100.68,
                ],
        ];
    }

    /**
     * @dataProvider correctDataSet
     * @param Transaction $transaction
     * @param float $expectedCommission
     * @return void
     */
    public function testCommissionWithCorrectBinAndRate(Transaction $transaction, float $expectedCommission): void
    {
        $commissionCalculator = new CommissionCalculator($this->mockedBinProvider, $this->mockedCurrencyRateProvider);
        $actualCommission = $commissionCalculator->calculate($transaction);
        $this->assertEquals($expectedCommission, $actualCommission);
    }

    public function incorrectDataSet(): array
    {
        return [
            'zeroCurrencyRate' => [
                new Transaction('1111', 1234, 'ABC')
            ],
        ];
    }

    /**
     * @dataProvider incorrectDataSet
     * @param Transaction $transaction
     * @return void
     */
    public function testCommissionWithZeroCurrencyRate(Transaction $transaction): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $commissionCalculator = new CommissionCalculator($this->mockedBinProvider, $this->mockedCurrencyRateProvider);
        $commissionCalculator->calculate($transaction);
    }
}