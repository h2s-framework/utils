<?php

namespace Siarko\Utils;

use Siarko\DependencyManager\DependencyManager;
use Siarko\DependencyManager\Type\TypedArgument;
use Siarko\DependencyManager\Type\TypedValue;
use Siarko\Utils\Code\Type\ArrayType;
use Siarko\Utils\Code\Type\SimpleType;
use Siarko\Utils\Code\Type\TypeInterface;
use Siarko\Utils\Code\Type\UnionType;
use Siarko\Utils\Exceptions\TypeCastException;

class TypeManager
{

    public const TYPE_INT = 'int';
    public const TYPE_STRING = 'string';
    public const TYPE_FLOAT = 'float';
    public const TYPE_BOOL = 'bool';
    public const TYPE_ITERABLE = 'iterable';
    public const TYPE_DOUBLE = 'double';
    public const TYPE_ARRAY = 'array';
    public const TYPE_MIXED = 'mixed';
    public const TYPE_OBJECT = 'object';
    public const TYPE_CONST = 'const';
    public const TYPE_PROVIDER = 'provider';
    public const PRIMITIVE_TYPES = [
        self::TYPE_INT,
        self::TYPE_STRING,
        self::TYPE_FLOAT,
        self::TYPE_BOOL,
        self::TYPE_DOUBLE,
        self::TYPE_ARRAY,
        self::TYPE_ITERABLE,
        self::TYPE_MIXED
    ];

    /**
     * @param DependencyManager $dependencyManager
     */
    public function __construct(
        protected readonly DependencyManager $dependencyManager
    )
    {
    }

    /**
     * @param string|TypeInterface $type
     * @param $value
     * @return array|bool|float|int|object|string|null
     * @throws TypeCastException
     */
    public function cast(string|TypeInterface $type, $value)
    {
        if ($type instanceof TypeInterface) {
            return $this->castUsingObject($type, $value);
        }
        return $this->castUsingName($type, $value);
    }

    /**
     * @param string $type
     * @return bool
     */
    public function isPrimitive(string $type): bool
    {
        return in_array(strtolower($type), self::PRIMITIVE_TYPES);
    }

    /**
     * @param TypeInterface $type
     * @param $value
     * @return mixed
     * @throws TypeCastException
     */
    private function castUsingObject(TypeInterface $type, $value): mixed
    {
        if($value === null && $type->isNullable()){
            return null;
        }
        if($type instanceof UnionType){
            throw new TypeCastException("Cannot cast union type");
        }
        if($type instanceof ArrayType){
            return $this->castArray($type, $value);
        }
        if($type instanceof SimpleType){
            if($type->isObjectType() || !$this->isPrimitive($type->getName())){
                return $this->castObject($type, $value);
            }else{
                return $this->castUsingName($type->getName(), $value);
            }
        }
        throw new TypeCastException("Unknown type ".get_class($type));
    }

    /**
     * @param ArrayType $type
     * @param $value
     * @return array
     */
    private function castArray(ArrayType $type, $value): array
    {
        if(is_null($value) || empty($value)){
            return [];
        }
        if(!is_array($value)){
            throw new TypeCastException("Trying to cast non-array value to array");
        }
        if($type->getType() === null){
            return $value;
        }
        $result = [];
        foreach ($value as $key => $item) {
            $result[$key] = $this->cast($type->getType(), $item);
        }
        return $result;
    }

    /**
     * @param SimpleType $type
     * @param $value
     * @return ?object
     * @throws TypeCastException
     */
    private function castObject(SimpleType $type, $value): ?object
    {
        if(is_null($value)){
            if($type->isNullable()){
                return null;
            }
            throw new TypeCastException("Trying to cast null to non-nullable object");
        }
        if(is_object($value)){
            return $value;
        }
        if(!is_string($value)){
            throw new TypeCastException("Trying to cast non-string value to object");
        }
        return $this->dependencyManager->get($value);
    }

    /**
     * @param string $type
     * @param mixed $value
     * @return mixed
     * @throws TypeCastException
     */
    private function castUsingName(string $type, mixed $value): mixed
    {
        [$type, $meta] = $this->extractMeta(strtolower($type));
        if($type === self::TYPE_MIXED) {
            return $value;
        }
        if ($type === self::TYPE_INT) {
            return intval($value);
        }
        if ($type === self::TYPE_FLOAT) {
            return floatval($value);
        }
        if ($type === self::TYPE_STRING) {
            return $value . "";
        }
        if ($type === self::TYPE_BOOL) {
            return strtolower($value) == "true" || $value == "1" || $value == 1 || $value;
        }
        if ($type === self::TYPE_OBJECT) {
            if(is_string($value)){
                if(in_array('unique', $meta)){
                    return $this->dependencyManager->create($value);
                }
                return $this->dependencyManager->get($value);
            }
            if(is_array($value)){
                $result = [];
                foreach ($value as $key => $item) {
                    $result[$key] = $this->cast($type, $item);
                }
                return $result;
            }
            throw new TypeCastException("Trying to create object out of non-string value - ".print_r($value));
        }
        if ($type === self::TYPE_ARRAY) {
            if(is_array($value)){
                return $value;
            }
            throw new TypeCastException("Trying to create array out of ".gettype($value));
        }
        if($type === self::TYPE_PROVIDER){
            if(!is_string($value)){
                throw new TypeCastException("Incorrect provider supplied, expected string reference, got \"".gettype($value).'"');
            }
            $reference = explode('::', $value);
            if(count($reference) != 2) {
                throw new TypeCastException("Incorrect provider definition, expected \"class::method\", got \"".$value.'"');
            }
            $methodName = $reference[1];
            $object = $this->dependencyManager->get($reference[0]);
            if(!method_exists($object, $methodName)){
                throw new TypeCastException("Trying to call non-existent method \"".$methodName."\" on object of class \"".get_class($object).'" during provider execution');
            }
            return $object->$methodName();
        }
        if($type === self::TYPE_CONST){
            return constant($value);
        }
        throw new TypeCastException("Trying to perform unknown type cast \"".$type."\" -> \"".gettype($value).'"');
    }

    /**
     * @param string $typeName
     * @return array
     */
    private function extractMeta(string $typeName): array
    {
        $parts = explode('(', $typeName);
        if(count($parts) == 1){
            return [$parts[0], []];
        }
        return [$parts[0], explode(',', rtrim($parts[1], ')'))];
    }
}