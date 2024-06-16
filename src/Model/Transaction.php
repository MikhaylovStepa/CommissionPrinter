<?php

declare(strict_types=1);

namespace Model;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

class Transaction
{
    /**
     * @var string|null
     * @Type("string")
     * @SerializedName("bin")
     */
    private $bin;

    /**
     * @var float|null
     * @Type("float")
     * @SerializedName("amount")
     */
    private $amount;

    /**
     * @var string|null
     * @Type("string")
     * @SerializedName("currency")
     */
    private $currency;

    public function __construct($bin, $amount, $currency)
    {
        $this->bin = $bin;
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function getBin(): ?string
    {
        return $this->bin;
    }

    public function setBin(?string $bin): void
    {
        $this->bin = $bin;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): void
    {
        $this->amount = $amount;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }
}