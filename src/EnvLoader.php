<?php

namespace Siarko\Utils;

use Dotenv\Dotenv;
use Siarko\Paths\Exception\RootPathNotSet;
use Siarko\Paths\RootPath;

class EnvLoader extends DynamicDataObject
{

    public function __construct(
        private readonly ArrayManager $arrayManager,
        RootPath $rootPath
    )
    {
        parent::__construct($this->loadEnv($rootPath));
    }

    /**
     * @param RootPath $rootPath
     * @return array
     * @throws RootPathNotSet
     */
    private function loadEnv(RootPath $rootPath): array
    {
        $dotEnv = Dotenv::createImmutable($rootPath->get());
        return $this->transformToAssoc($dotEnv->safeLoad() ?? []);
    }

    /**
     * @param array $envData
     * @return array
     */
    private function transformToAssoc(array $envData): array
    {
        $result = [];
        foreach ($envData as $key => $value) {
            if(!preg_match('/[a-z]/', $key)){
                $key = strtolower($key);
            }
            $this->arrayManager->set($key, $result, $value, '.');
        }
        return $result;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getData(string $name): mixed
    {
        return $this->arrayManager->get($name, $this->__data, null, '.');
    }


}