<?php

namespace Siarko\Utils\Sorting;

use MJS\TopSort\CircularDependencyException;
use MJS\TopSort\ElementNotFoundException;
use MJS\TopSort\Implementations\FixedArraySort;

class BeforeAfterSort
{

    public const WORD_BEFORE = 'before';
    public const WORD_AFTER = 'after';

    /**
     * @description Sort array of dependent data using before and after words
     *              Keys are Ids of items
     *              before and after words are used to define ids of items that should be before or after current item
     *
     * @param array $items
     * @param string $wordBefore
     * @param string $wordAfter
     * @param bool $unsetWords
     * @return array
     * @throws CircularDependencyException
     * @throws ElementNotFoundException
     */
    public function sort(
        array $items,
        string $wordBefore = self::WORD_BEFORE,
        string $wordAfter = self::WORD_AFTER,
        bool $unsetWords = true
    ): array {
        $sorter = new FixedArraySort();
        $sorter->set($this->createDependencies($items, $wordBefore, $wordAfter));

        if($unsetWords){
            return array_map(function($id) use ($items, $wordBefore, $wordAfter) {
                $value = $items[$id];
                unset($items[$wordBefore], $items[$wordAfter]);
                return $value;
            }, $sorter->sort());
        }

        return array_map(function($id) use ($items, $wordBefore, $wordAfter) {
            return $items[$id];
        }, $sorter->sort());
    }

    /**
     * @param array $data
     * @param string $wordBefore
     * @param string $wordAfter
     * @return array
     */
    private function createDependencies(array $data, string $wordBefore, string $wordAfter): array
    {
        $result = [];
        foreach ($data as $id => $row) {
            $result[$id] = $result[$id] ?? [];
            if(is_array($row)){
                if($before = $row[$wordBefore] ?? null){
                    $result[$before][] = $id;
                }
                if($after = $row[$wordAfter] ?? null){
                    $result[$id][] = $after;
                }
            }
        }
        return $result;
    }
}