<?php

namespace Siarko\Utils\Code\Type;

class SimpleType extends AbstractType
{

    public function __construct(
        private readonly string $name,
        private readonly ?bool $builtIn = false,
        bool $nullable = false
    )
    {
        parent::__construct($nullable);
    }

    /**
     * @return bool
     */
    public function isObjectType(): bool
    {
        return (is_null($this->builtIn) ? str_contains($this->name, '\\') : !$this->builtIn);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

}