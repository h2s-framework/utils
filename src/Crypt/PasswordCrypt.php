<?php

namespace Siarko\Utils\Crypt;

use Siarko\Utils\Crypt\Algorithm\AbstractAlgorithm;
use Siarko\Utils\Crypt\Algorithm\CustomAlgorithm;

class PasswordCrypt
{

    protected ?AbstractAlgorithm $algorithm = null;

    /**
     * @return AbstractAlgorithm
     */
    public function getAlgorithm(): AbstractAlgorithm
    {
        if($this->algorithm === null){
            $this->algorithm = new CustomAlgorithm();
        }
        return $this->algorithm;
    }

    /**
     * Null is default
     * @param AbstractAlgorithm|null $algorithm
     */
    public function setAlgorithm(?AbstractAlgorithm $algorithm): void
    {
        $this->algorithm = $algorithm;
    }

    /**
     * @param string $value
     * @return string
     */
    public function encrypt(string $value): string{
        return password_hash($value, $this->getAlgorithm()->getAlgoName(), $this->getAlgorithm()->getAlgoOptions());
    }

    /**
     * @param string $value
     * @param string $hash
     * @return bool
     */
    public function match(string $value, string $hash): bool
    {
        return password_verify($value, $hash);
    }

    /**
     * @param string $hash
     * @return bool
     */
    public function shouldRehash(string $hash): bool
    {
        return password_needs_rehash($hash, $this->getAlgorithm()->getAlgoName());
    }

}