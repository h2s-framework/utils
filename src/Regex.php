<?php

namespace Siarko\Utils;

class Regex
{

    /**
     * @param string $pattern
     * @param $target
     * @param bool $clearNumericKeys
     * @return array
     */
    public function match(string $pattern, $target, bool $clearNumericKeys = false): array{
        $result = [];
        preg_match($pattern, $target, $result);
        if($clearNumericKeys){
            foreach ($result as $key => $value) {
                if(is_numeric($key)){
                    unset($result[$key]);
                }
            }
        }
        return $result;
    }

    /**
     * @param string $pattern
     * @param $target
     * @return array
     */
    public function matchAll(string $pattern, $target): array{
        $result = [];
        preg_match_all($pattern, $target, $result);
        return $result;
    }

}