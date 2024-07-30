<?php

namespace Siarko\Utils\Persistance\Messaging;

use JetBrains\PhpStorm\Pure;
use Siarko\Serialization\Api\Attribute\Serializable;

class SimpleMessage implements MessageInterface
{
    public const TYPE_INFO = 'info';
    public const TYPE_ERROR = 'error';
    public const TYPE_WARNING = 'warning';
    public const TYPE_SUCCESS = 'success';

    public const COLOR_MAP = [
        self::TYPE_INFO => 'info',
        self::TYPE_ERROR => 'danger',
        self::TYPE_WARNING => 'warning',
        self::TYPE_SUCCESS => 'success'
    ];

    /**
     * @param string $type
     * @param string $text
     * @param string|null $color
     */
    public function __construct(
        #[Serializable] protected string $type = 'info',
        #[Serializable] protected string $text = '',
        #[Serializable] protected ?string $color = null
    )
    {
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @param string|null $color
     */
    public function setColor(?string $color): void
    {
        $this->color = $color;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $default
     * @return string
     */
    #[Pure] public function getColor(string $default = ''): string
    {
        if($this->color == null){
            if(array_key_exists($this->getType(), self::COLOR_MAP)){
                return self::COLOR_MAP[$this->getType()];
            }
            return $default;
        }
        return $this->color;
    }
}