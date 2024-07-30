<?php

namespace Siarko\Utils\Persistance\Session;

use Siarko\Utils\ArrayManager;

class SessionManager
{

    public function __construct(
        protected readonly ArrayManager $arrayManager
    )
    {
    }

    public function open(){
        session_start();
    }

    public function get(string|array $name, $default = null){
        return $this->arrayManager->get($name, $_SESSION, $default);
    }

    public function set(string|array $name, $value){
        $this->arrayManager->set($name, $_SESSION, $value);
    }

    public function getId(): string
    {
        return session_id();
    }

    public function setId($sessionId): void
    {
        session_id($sessionId);
    }

    /**
     * Flush all session values
     */
    public function flush(){
        session_unset();
    }

}