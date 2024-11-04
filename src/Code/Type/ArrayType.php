<?php

namespace Siarko\Utils\Code\Type;

class ArrayType extends AbstractType
{

    /**
     * @param TypeInterface|null $type
     * @param bool $nullable
     */
    public function __construct(
        private readonly ?TypeInterface $type,
        bool $nullable = false
    )
    {
        parent::__construct($nullable);
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

}