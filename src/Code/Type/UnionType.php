<?php

namespace Siarko\Utils\Code\Type;

class UnionType extends AbstractType
{

    /**
     * @param TypeInterface[] $types
     */
    public function __construct(
        private readonly array $types
    )
    {
        parent::__construct(null);
    }

    /**
     * @return array
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable ?? ($this->nullable = $this->evaluateNullable());
    }

    /**
     * @return bool
     */
    private function evaluateNullable(): bool
    {
        foreach ($this->types as $type) {
            if($type->isNullable()){
                return true;
            }
        }
        return false;
    }

}