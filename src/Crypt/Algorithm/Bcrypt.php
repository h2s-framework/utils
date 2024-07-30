<?php

namespace Siarko\Utils\Crypt\Algorithm;

class Bcrypt extends AbstractAlgorithm
{
    public function __construct(
        protected int $cost = 10
    )
    {
    }

    /**
     * @return int
     */
    public function getCost(): int
    {
        return $this->cost;
    }

    /**
     * @param int $cost
     */
    public function setCost(int $cost): void
    {
        $this->cost = $cost;
    }

    /**
     * @return string
     */
    public function getAlgoName(): string
    {
        return PASSWORD_BCRYPT;
    }

    /**
     * @return int[]
     */
    public function getAlgoOptions(): array
    {
        return [
            'cost' => $this->cost
        ];
    }
}