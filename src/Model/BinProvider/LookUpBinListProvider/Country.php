<?php

declare(strict_types=1);

namespace Model\BinProvider\LookUpBinListProvider;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

class Country
{
    /**
     * @var string|null
     * @Type("string")
     * @SerializedName("alpha2")
     */
    private $alpha2;

    public function getAlpha2(): ?string
    {
        return $this->alpha2;
    }

    public function setAlpha2(?string $alpha2): void
    {
        $this->alpha2 = $alpha2;
    }
}