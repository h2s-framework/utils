<?php

namespace Siarko\Utils\Crypt\Algorithm;

class Argon2ID extends Argon2I
{
    public function getAlgoName(): string
    {
        return PASSWORD_ARGON2ID;
    }

}