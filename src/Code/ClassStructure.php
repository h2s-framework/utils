<?php

namespace Siarko\Utils\Code;

use ReflectionClass;
use ReflectionException;

class ClassStructure
{

    public const CONSTRUCTOR_METHOD_NAME = '__construct';

    protected ReflectionClass $reflection;

    /**
     * @var MethodStructure[]
     */
    protected array $methods = [];

    /**
     * @var ClassPropertyStructure[]
     */
    protected array $properties = [];

    protected array $parentClasses;

    /**
     * @param string $className
     * @throws ReflectionException
     */
    public function __construct(
        protected readonly string $className
    )
    {
        $this->reflection = new ReflectionClass($className);
    }

    /**
     * @return MethodStructure[]
     */
    public function getMethods(?int $filter = null): array
    {
        $result = [];
        foreach ($this->reflection->getMethods($filter) as $reflectionMethod) {
            $result[$reflectionMethod->getName()] = $this->getMethod($reflectionMethod->getName());
        }
        return $result;
    }

    /**
     * @param string $name
     * @return MethodStructure|null
     */
    public function getMethod(string $name): ?MethodStructure
    {
        if(!array_key_exists($name, $this->methods)){
            try{
                $this->methods[$name] = new MethodStructure($this, $this->reflection->getMethod($name));
            }catch (ReflectionException){
                $this->methods[$name] = null;
            }
        }
        return $this->methods[$name];
    }

    /**
     * @return MethodStructure|null
     */
    public function getConstructor(): ?MethodStructure
    {
        return $this->getMethod(self::CONSTRUCTOR_METHOD_NAME);
    }

    /**
     * @param int|null $filter
     * @return array
     */
    public function getProperties(?int $filter = null): array
    {
        $result = [];
        foreach ($this->reflection->getProperties($filter) as $reflectionProperty) {
            $result[$reflectionProperty->getName()] = $this->getProperty($reflectionProperty->getName());
        }
        return $result;
    }

    /**
     * @param string $name
     * @return ClassPropertyStructure|null
     */
    public function getProperty(string $name): ?ClassPropertyStructure
    {
        if(!array_key_exists($name, $this->properties)){
            try{
                $this->properties[$name] = new ClassPropertyStructure($this->reflection->getProperty($name));
            }catch (ReflectionException){
                $this->properties[$name] = null;
            }
        }
        return $this->properties[$name];
    }

    /**
     * @return bool
     */
    public function isInstantiable(): bool
    {
        return $this->reflection->isInstantiable();
    }

    /**
     * @return array
     */
    public function getParentClasses(): array
    {
        if(!isset($this->parentClasses)){
            $this->parentClasses = [];
            $parent = $this->reflection;
            while(($parent = $parent->getParentClass()) !== false){
                $this->parentClasses[] = $parent->getName();
            }
        }
        return $this->parentClasses;
    }

    /**
     * @return bool
     */
    public function hasCustomConstructor(): bool
    {
        return !is_null($this->reflection->getConstructor());
    }

    /**
     * @param array $arguments
     * @return object
     * @throws \ReflectionException
     */
    public function createInstance(array $arguments = []): object
    {
        if (empty($arguments)) {
            return $this->reflection->newInstance();
        }
        // because of fcking php reflection inconsistencies
        if ($this->reflection->getConstructor()?->getNumberOfParameters() == 1) {
            return $this->reflection->newInstance(reset($arguments));
        }
        return $this->reflection->newInstance(...$arguments);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->reflection->getName();
    }

    /**
     * @return ReflectionClass
     */
    public function getNativeReflection(): ReflectionClass
    {
        return $this->reflection;
    }

}