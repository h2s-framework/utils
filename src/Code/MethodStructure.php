<?php

namespace Siarko\Utils\Code;

use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use ReflectionMethod;
use Siarko\Utils\Code\DocBlock\StaticParser;

class MethodStructure
{

    /**
     * @var MethodParameterStructure[]
     */
    protected array $parameters;

    protected ?PhpDocNode $docBlock;

    /**
     * @param ClassStructure $class
     * @param ReflectionMethod $reflection
     */
    public function __construct(
        protected readonly ClassStructure $class,
        protected readonly ReflectionMethod $reflection
    )
    {
    }

    /**
     * @return ClassStructure
     */
    public function getDeclaringClass(): ClassStructure
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->reflection->getName();
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        if(!isset($this->parameters)){
            $this->parameters = [];
            foreach ($this->reflection->getParameters() as $parameter) {
                $this->parameters[] = new MethodParameterStructure($this, $parameter);
            }
        }
        return $this->parameters;
    }

    /**
     * @param string $name
     * @return MethodParameterStructure|null
     */
    public function getParameter(string $name): ?MethodParameterStructure
    {
        return $this->getParameters()[$name] ?? null;
    }

    /**
     * @return PhpDocNode|null
     */
    public function getDocBlock(): ?PhpDocNode
    {
        if(!isset($this->docBlock)){
            $docComment = $this->reflection->getDocComment();
            if(!$docComment){
                $this->docBlock = null;
            }else{
                $this->docBlock = StaticParser::parse($docComment);
            }
        }
        return $this->docBlock;
    }

    /**
     * @return ReflectionMethod
     */
    public function getNativeReflection(): ReflectionMethod
    {
        return $this->reflection;
    }

}