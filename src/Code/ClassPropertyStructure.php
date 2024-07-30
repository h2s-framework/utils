<?php

namespace Siarko\Utils\Code;

use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use ReflectionProperty;
use Siarko\Utils\Code\DocBlock\StaticParser;
use Siarko\Utils\Code\Type\TypeBuilder;
use Siarko\Utils\Code\Type\TypeInterface;

class ClassPropertyStructure
{

    private null|bool|TypeInterface $type = false;
    private null|bool|PhpDocNode $docBlock = false;
    private readonly TypeBuilder $typeBuilder;

    /**
     * @param ReflectionProperty $reflection
     */
    public function __construct(
        private readonly ReflectionProperty $reflection
    )
    {
        $this->typeBuilder = new TypeBuilder();
    }

    /**
     * @template T
     *
     * @param class-string<T>|null $name
     * @param int $flags
     * @return ReflectionProperty<T>[]
     */
    public function getAttributes(?string $name = null, int $flags = 0): array
    {
        return $this->reflection->getAttributes($name, $flags);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->reflection->getName();
    }

    /**
     * @return TypeInterface
     */
    public function getType(): TypeInterface
    {
        if($this->type === false){
            $this->type = $this->constructType();
        }
        return $this->type;
    }

    /**
     * @return PhpDocNode|null
     */
    public function getDocBlock(): ?PhpDocNode
    {
        if($this->docBlock === false){
            $docBlock = $this->reflection->getDocComment();
            $this->docBlock = $docBlock ? StaticParser::parse($docBlock) : null;
        }
        return $this->docBlock;
    }

    /**
     * @return ReflectionProperty
     */
    public function getNativeReflection(): ReflectionProperty
    {
        return $this->reflection;
    }

    /**
     * @return TypeInterface|null
     */
    private function constructType(): ?TypeInterface
    {
        $reflectionType = $this->reflection->getType();
        if(!$reflectionType){
            return null;
        }
        $varTag = null;
        $docBlock = $this->getDocBlock();
        if($docBlock){
            $varTag = current(array_filter(
                $docBlock->getTagsByName('@var'),
                fn($tag) => !$tag->value->variableName || $tag->value->variableName === '$'.$this->getName()
            ));
            if(!$varTag){ $varTag = null; }
        }
        return $this->typeBuilder->build(
            reflectionType: $reflectionType,
            annotatedType: $varTag?->value?->type
        );
    }
}