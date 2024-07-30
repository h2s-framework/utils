<?php

namespace Siarko\Utils;

class DynamicDataObject
{

    public function __construct(array $data = []) {
        $this->setAllData($data);
    }

    /**
     * @var array
     */
    protected array $__data = [];

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set(string $name, mixed $value): void
    {
        $this->__data[$name] = $value;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function __get(string $name): mixed
    {
        return $this->__data[$name] ?? null;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed|void|null
     */
    public function __call(string $name, array $arguments)
    {
        $type = substr($name, 0, 3);
        $property = lcfirst(substr($name, 3));
        if ($type === 'get') {
            return $this->__get($property);
        } elseif ($type === 'set') {
            $this->__set($property, $arguments[0]);
        }
        return null;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function setData(string $name, mixed $value): self
    {
        $this->__data[$name] = $value;
        return $this;
    }

    /**
     * @param array $data
     * @return void
     */
    public function setAllData(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->setData($key, $value);
        }
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getData(string $name): mixed
    {
        return $this->__data[$name] ?? null;
    }

    /**
     * @return array
     */
    public function getAllData(): array
    {
        return $this->toArray();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return isset($this->__data[$name]);
    }

    /**
     * @param string $name
     * @return void
     */
    public function __unset(string $name): void
    {
        unset($this->__data[$name]);
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        return $this->__data;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->__data;
    }

}