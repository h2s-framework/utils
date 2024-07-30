<?php

namespace Siarko\Utils\Code\DocBlock;

use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;

/**
 * Class StaticParser
 * Static context for parsing doc blocks
 */
class StaticParser
{

    protected  static Lexer $lexer;

    protected  static ConstExprParser $constExprParser;

    protected  static TypeParser $typeParser;
    protected  static PhpDocParser $phpDocParser;

    /**
     * @return void
     */
    protected static function initialize(): void
    {
        if(isset(static::$lexer)){
            return;
        }
        static::$lexer = new Lexer();
        static::$constExprParser = new ConstExprParser();
        static::$typeParser = new TypeParser(static::$constExprParser);
        static::$phpDocParser = new PhpDocParser(static::$typeParser, static::$constExprParser);
    }

    /**
     * @param string $docBlock
     * @return PhpDocNode|null
     */
    public static function parse(string $docBlock): ?PhpDocNode
    {
        if(empty($docBlock)){
            return null;
        }
        static::initialize();
        $tokens = static::$lexer->tokenize($docBlock);
        return static::$phpDocParser->parse(new TokenIterator($tokens));
    }

}