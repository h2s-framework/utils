<?php

namespace Siarko\Utils\Persistance\Messaging;

interface MessageInterface
{
    public function getText(): string;

    public function getType(): string;

    public function getColor(): string;

}