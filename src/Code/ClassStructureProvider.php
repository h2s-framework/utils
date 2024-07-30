<?php

namespace Siarko\Utils\Code;

class ClassStructureProvider
{

    /**
     * @param array $classStructures
     */
    public function __construct(
        protected array $classStructures = []
    )
    {
    }

    /**
     * @param string $className
     * @return ClassStructure
     * @throws \ReflectionException
     */
    public function get(string $className): ClassStructure
    {
        $className = trim($className, '\\');
        if(!array_key_exists($className, $this->classStructures)){
            $this->classStructures[$className] = new ClassStructure($className);
        }
        return $this->classStructures[$className];
    }

}