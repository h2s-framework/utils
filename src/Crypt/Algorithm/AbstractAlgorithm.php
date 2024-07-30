<?php

namespace Siarko\Utils\Crypt\Algorithm;

abstract class AbstractAlgorithm
{
    public abstract function getAlgoName(): string;

    public abstract function getAlgoOptions(): array;

}