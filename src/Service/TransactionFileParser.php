<?php

declare(strict_types=1);

namespace Service;

use JMS\Serializer\SerializerInterface;
use Model\Transaction;

class TransactionFileParser
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param string $path
     * @return Transaction[]
     */
    public function parse(string $path): array
    {
        $lines = explode(PHP_EOL, file_get_contents($path));
        $transactions = [];
        foreach ($lines as $line) {
            $transactions[] = $this->serializer->deserialize($line, Transaction::class, 'json');
        }

        return $transactions;
    }
}