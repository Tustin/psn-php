<?php

namespace Tests\Traits;

use http\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

class OperandParserTest extends TestCase
{

    public function testItShouldParseEquals(): void
    {
        $parser = new OperandParserTester('=');
        $this->assertTrue($parser->parseIt(2, 2));
        $this->assertFalse($parser->parseIt(3, 2));
        $this->assertFalse($parser->parseIt('2', 2));

        $parser = new OperandParserTester('==');
        $this->assertTrue($parser->parseIt(2, 2));
        $this->assertFalse($parser->parseIt(3, 2));
        $this->assertFalse($parser->parseIt('2', 2));

        $parser = new OperandParserTester('===');
        $this->assertTrue($parser->parseIt(2, 2));
        $this->assertFalse($parser->parseIt(3, 2));
        $this->assertFalse($parser->parseIt('2', 2));
    }

    public function testItShouldParseNotEquals(): void
    {
        $parser = new OperandParserTester('<>');
        $this->assertFalse($parser->parseIt(2, 2));
        $this->assertTrue($parser->parseIt(3, 2));
        $this->assertTrue($parser->parseIt('2', 2));

        $parser = new OperandParserTester('!=');
        $this->assertFalse($parser->parseIt(2, 2));
        $this->assertTrue($parser->parseIt(3, 2));
        $this->assertTrue($parser->parseIt('2', 2));
    }

    public function testItShouldParseBiggerThan(): void
    {
        $parser = new OperandParserTester('>');
        $this->assertTrue($parser->parseIt(3, 2));
        $this->assertTrue($parser->parseIt('b', 'a'));
        $this->assertFalse($parser->parseIt(2, 2));
        $this->assertFalse($parser->parseIt(1, 2));
    }

    public function testItShouldParseBiggerThanOrEquals(): void
    {
        $parser = new OperandParserTester('>=');
        $this->assertTrue($parser->parseIt(3, 2));
        $this->assertTrue($parser->parseIt('b', 'a'));
        $this->assertTrue($parser->parseIt('a', 'a'));
        $this->assertTrue($parser->parseIt(2, 2));
        $this->assertFalse($parser->parseIt(1, 2));
    }

    public function testItShouldParseSmallerThan(): void
    {
        $parser = new OperandParserTester('<');
        $this->assertFalse($parser->parseIt(3, 2));
        $this->assertFalse($parser->parseIt('b', 'a'));
        $this->assertFalse($parser->parseIt(2, 2));
        $this->assertTrue($parser->parseIt(1, 2));
    }

    public function testItShouldParseSmallerThanOrEquals(): void
    {
        $parser = new OperandParserTester('<=');
        $this->assertFalse($parser->parseIt(3, 2));
        $this->assertFalse($parser->parseIt('b', 'a'));
        $this->assertTrue($parser->parseIt('a', 'a'));
        $this->assertTrue($parser->parseIt(2, 2));
        $this->assertTrue($parser->parseIt(1, 2));
    }

    public function testItShouldThrowOnInvalidOperator(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Operator value [!!] is not supported.");

        $parser = new OperandParserTester('!!');
        $parser->parseIt(3, 2);
    }
}
