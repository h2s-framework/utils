<?php

namespace Siarko\Utils\Code\Type;

use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use ReflectionType;

class TypeBuilder
{

    /**
     * @param ReflectionType $reflectionType
     * @param TypeNode|null $annotatedType
     * @return TypeInterface|null
     */
    public function build(ReflectionType $reflectionType, ?TypeNode $annotatedType): ?TypeInterface
    {
        $annotatedType = $this->unifyNullables($annotatedType);
        if($reflectionType instanceof \ReflectionUnionType){
            return $this->buildUnion($reflectionType, $annotatedType);
        }
        if($reflectionType instanceof \ReflectionNamedType){
            return $this->buildNamed($reflectionType, $annotatedType);
        }
        return null;
    }

    /**
     * @param ReflectionType $reflectionType
     * @param TypeNode|null $annotatedType
     * @return UnionType
     */
    private function buildUnion(ReflectionType $reflectionType, ?TypeNode $annotatedType): UnionType
    {
        $types = [];
        foreach ($reflectionType->getTypes() as $index => $type) {
            $types[] = $this->buildNamed($type, null);
        }
        return new UnionType($types);
    }

    /**
     * @param \ReflectionNamedType $reflectionType
     * @param TypeNode|null $annotatedType
     * @return TypeInterface
     */
    private function buildNamed(\ReflectionNamedType $reflectionType, ?TypeNode $annotatedType): TypeInterface
    {
        if($reflectionType->getName() === 'array') {
            return $this->buildArrayType($reflectionType, $annotatedType);
        }
        return new SimpleType($reflectionType->getName(), $reflectionType->allowsNull());
    }

    /**
     * @param \ReflectionNamedType $reflectionType
     * @param TypeNode|null $annotatedType
     * @return ArrayType
     */
    private function buildArrayType(\ReflectionNamedType $reflectionType, ?TypeNode $annotatedType): ArrayType
    {
        if($annotatedType instanceof ArrayTypeNode){
            return $this->recurrentArrayParser($annotatedType);
        }
        return new ArrayType(null, $reflectionType->allowsNull());
    }

    /**
     * @param ArrayTypeNode $annotatedType
     * @return ArrayType
     */
    private function recurrentArrayParser(ArrayTypeNode $annotatedType): ArrayType
    {
        if($annotatedType->type instanceof ArrayTypeNode){
            return new ArrayType($this->recurrentArrayParser($annotatedType->type));
        }
        if($annotatedType->type instanceof IdentifierTypeNode){
            return new ArrayType(new SimpleType($annotatedType->type->name));
        }
        return new ArrayType(null);
    }

    /**
     * @param TypeNode|null $annotatedType
     * @return TypeNode|null
     */
    private function unifyNullables(?TypeNode $annotatedType): ?TypeNode
    {
        if(is_null($annotatedType)) { return null; }
        if($annotatedType instanceof UnionTypeNode && count($annotatedType->types) === 2){
            $typeZero = $annotatedType->types[0];
            $typeOne = $annotatedType->types[1];
            $realType = null;
            if($typeZero instanceof IdentifierTypeNode && $typeZero->name == 'null'){
                $realType = $typeOne;
            }
            if($typeOne instanceof IdentifierTypeNode && $typeOne->name == 'null'){
                $realType = $typeZero;
            }
            if($realType){
                return new NullableTypeNode($realType);
            }
        }
        return $annotatedType;
    }

}