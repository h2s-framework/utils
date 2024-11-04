<?php

namespace Siarko\Utils\Persistance\Messaging;

use Siarko\Serialization\Json\JsonSerializer;
use Siarko\Utils\ArrayManager;
use Siarko\Utils\Persistance\Session\SessionManager;

class MessageManager
{

    public const MESSAGES_KEY = 'messages';


    public function __construct(
        protected readonly ArrayManager $arrayManager,
        protected readonly SimpleMessageFactory $simpleMessageFactory,
        protected readonly SessionManager $sessionManager,
        protected readonly JsonSerializer $serializer
    )
    {
    }

    /**
     * @param MessageInterface $message
     * @throws \JsonException
     */
    public function addMessage(MessageInterface $message){
        $messages = $this->sessionManager->get(self::MESSAGES_KEY);
        $this->arrayManager->set('*', $messages, $this->serializer->serialize($message));
        $this->sessionManager->set(self::MESSAGES_KEY, $messages);
    }

    /**
     * @param string $value
     * @param string $type
     */
    public function addSimpleMessage(string $value, string $type = 'info')
    {
        $this->addMessage($this->simpleMessageFactory->create([
            'type' => $type,
            'text' => $value
        ]));
    }

    /**
     * @param string $text
     */
    public function error(string $text)
    {
        $this->addSimpleMessage($text, SimpleMessage::TYPE_ERROR);
    }

    /**
     * @param string $text
     */
    public function success(string $text)
    {
        $this->addSimpleMessage($text, SimpleMessage::TYPE_SUCCESS);
    }

    /**
     * @param string $text
     */
    public function warning(string $text)
    {
        $this->addSimpleMessage($text, SimpleMessage::TYPE_WARNING);
    }

    /**
     * @param string $text
     */
    public function info(string $text)
    {
        $this->addSimpleMessage($text, SimpleMessage::TYPE_INFO);
    }

    /**
     * @param string|null $type
     * @param bool $clear
     * @return array
     * @throws \ReflectionException
     */
    public function getMessages(?string $type = null, bool $clear = true): array
    {
        $messages = $this->sessionManager->get(self::MESSAGES_KEY, []);
        if($clear){
            $this->sessionManager->set(self::MESSAGES_KEY, []);
        }
        $result = [];
        foreach ($messages as $msgType => $message) {
            if($type == null || ($type == $msgType)){
                $result[] = $this->serializer->deserialize($message);
            }
        }
        return $result;
    }



}