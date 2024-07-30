<?php

namespace Siarko\Utils\Code\Type;

class ArrayType implements TypeInterface
{

    /**
     * @param TypeInterface|null $type
     * @param bool $nullable
     */
    public function __construct(
        private readonly ?TypeInterface $type,
        private readonly bool $nullable = false
    )
    {
    }

    /**
     * @return bool
     */
    public function hasSpecificType(): bool
    {
        return is_null($this->type);
    }

    /**
     * @return ?TypeInterface
     */
    public function getType(): ?TypeInterface
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

}