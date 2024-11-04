<?php

namespace Siarko\Utils\Persistance\Session;

use Siarko\Utils\ArrayManager;
use Siarko\Utils\Persistance\Cookie\CookieManager;

class SessionManager
{

    public function __construct(
        protected readonly ArrayManager $arrayManager,
        private readonly CookieManager $cookieManager
    )
    {
    }

    /**
     * If name is provided, session will be named and id will be stored in cookie
     * This means that you can have multiple sessions per user at the time
     * @param string|null $name
     * @return void
     */
    public function open(?string $name = null): void
    {
        if($name){
            session_name($name);
            $id = $this->cookieManager->get($name) ?? $this->createId();
            $this->setId($id);
            session_start();
            session_regenerate_id(true);
            $this->cookieManager->set($name, $this->getId());
        }else{
            session_start();
        }
    }

    public function close(){
        session_write_close();
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

    public function createId(): string
    {
        return session_create_id();
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