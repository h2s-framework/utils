<?php

namespace Siarko\Utils\Code\Type;

abstract class AbstractType implements TypeInterface
{

    protected function __construct(
        protected ?bool $nullable = false
    )
    {
    }


    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

}