<?php

namespace Siarko\Utils\Crypt\Algorithm;

class Argon2I extends AbstractAlgorithm
{

    public function __construct(
        protected int $memoryCost = PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
        protected int $timeCost = PASSWORD_ARGON2_DEFAULT_TIME_COST,
        protected int $threads = PASSWORD_ARGON2_DEFAULT_THREADS
    )
    {
    }

    /**
     * @return int
     */
    public function getMemoryCost(): int
    {
        return $this->memoryCost;
    }

    /**
     * @param int $memoryCost
     */
    public function setMemoryCost(int $memoryCost): void
    {
        $this->memoryCost = $memoryCost;
    }

    /**
     * @return int
     */
    public function getTimeCost(): int
    {
        return $this->timeCost;
    }

    /**
     * @param int $timeCost
     */
    public function setTimeCost(int $timeCost): void
    {
        $this->timeCost = $timeCost;
    }

    /**
     * @return int
     */
    public function getThreads(): int
    {
        return $this->threads;
    }

    /**
     * @param int $threads
     */
    public function setThreads(int $threads): void
    {
        $this->threads = $threads;
    }

    /**
     * @return string
     */
    public function getAlgoName(): string
    {
        return PASSWORD_ARGON2I;
    }

    /**
     * @return int[]
     */
    public function getAlgoOptions(): array
    {
        return [
            'memory_cost' => $this->getMemoryCost(),
            'time_cost' => $this->getTimeCost(),
            'threads' => $this->getThreads()
        ];
    }
}