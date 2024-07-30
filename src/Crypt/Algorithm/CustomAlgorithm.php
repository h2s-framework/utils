<?php

namespace Siarko\Utils\Crypt\Algorithm;

class CustomAlgorithm extends AbstractAlgorithm
{

    /**
     * @param string $name
     * @param array $options
     */
    public function __construct(
        protected string $name = PASSWORD_DEFAULT,
        protected array $options = []
    )
    {
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getAlgoName(): string
    {
        return $this->getName();
    }

    /**
     * @return array
     */
    public function getAlgoOptions(): array
    {
        return $this->getOptions();
    }
}