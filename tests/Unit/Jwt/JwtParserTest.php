<?php

namespace Tests\Unit\Jwt;

use PHPUnit\Framework\TestCase;

use App\Exceptions\JwtParseException;
use App\Jwt\Parser;
use JsonException;

class JwtParserTest extends TestCase
{
    public function testParseParts(): void
    {
        $this->expectException(JwtParseException::class);

        Parser::parse('string');
    }

    public function testParseNotBase64(): void
    {
        $this->expectException(JwtParseException::class);

        Parser::parse('string@.string#.string*');
    }

    public function testParseNotJson(): void
    {
        $this->expectException(JwtParseException::class);

        Parser::parse('dfksld@.dl#.dsl=');
    }


}
