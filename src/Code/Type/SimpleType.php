<?php

namespace Siarko\Utils\Code\Type;

class SimpleType implements TypeInterface
{

    public function __construct(
        private readonly string $name,
        private readonly bool $nullable = false
    )
    {
    }

    /**
     * @return bool
     */
    public function isObjectType(): bool
    {
        return str_contains($this->name, '\\');
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }



}