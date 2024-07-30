<?php

namespace Siarko\Utils\Code;

use ReflectionParameter;
use Siarko\Utils\Code\Type\TypeBuilder;
use Siarko\Utils\Code\Type\TypeInterface;

class MethodParameterStructure
{
    private readonly ?TypeInterface $type;
    private bool $typeBuilt = false;

    private readonly TypeBuilder $typeBuilder;
    /**
     * @param MethodStructure $method
     * @param ReflectionParameter $reflection
     */
    public function __construct(
        private readonly MethodStructure $method,
        private readonly ReflectionParameter $reflection
    )
    {
        $this->typeBuilder = new TypeBuilder();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->reflection->getName();
    }

    /**
     * @return TypeInterface|null
     */
    public function getType(): ?TypeInterface
    {
        if(!$this->typeBuilt){
            $this->type = $this->constructType();
            $this->typeBuilt = true;
        }
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isDefaultValueAvailable(): bool
    {
        return $this->reflection->isDefaultValueAvailable();
    }

    /**
     * @return mixed
     * @throws \ReflectionException
     */
    public function getDefaultValue(): mixed
    {
        if(!$this->isDefaultValueAvailable()){
            return null;
        }
        return $this->reflection->getDefaultValue();
    }
    /**
     * @return TypeInterface|null
     */
    private function constructType(): ?TypeInterface
    {
        $reflectionType = $this->reflection->getType();
        if(!$reflectionType) {
            return null;
        }
        $methodDocBlock = $this->method->getDocBlock();
        $paramTag = null;
        if($methodDocBlock){
            $paramTag = current(array_filter(
                $methodDocBlock->getTagsByName('@param'),
                fn($tag) => $tag->value->parameterName === '$'.$this->getName()
            ));
            if(!$paramTag){ $paramTag = null; }
        }
        return $this->typeBuilder->build(
            reflectionType: $this->reflection->getType(),
            annotatedType: $paramTag?->value->type
        );
    }

}