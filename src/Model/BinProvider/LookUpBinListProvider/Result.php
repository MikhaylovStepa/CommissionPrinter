<?php

declare(strict_types=1);

namespace Model\BinProvider\LookUpBinListProvider;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

class Result
{
    /**
     * @var Country|null
     * @Type("Model\BinProvider\LookUpBinListProvider\Country")
     * @SerializedName("country")
     */
    private $country;

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): void
    {
        $this->country = $country;
    }
}