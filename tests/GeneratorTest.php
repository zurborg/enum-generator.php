<?php

use PHPUnit\Framework\TestCase;

class GeneratorTest extends TestCase
{
    public function test()
    {
        $x = new \Enum\Generator('\\X\\Y\\Z', ['A' => null, 'B' => 'null']);
        $this->assertInstanceOf(\Enum\Generator::class, $x);
        $this->assertSame('X\\Y', $x->getNamespace());
        $this->assertSame('X\\Y\\Z', $x->getFullname());
        $this->assertSame('X/Y/Z.php', $x->getFilename());
        $this->assertSame('X/Y/Z.abc', $x->getFilename('abc'));
        $x->saveIntoFile('tmp');

        $lines = \Enum\Generator::build('\\X', []);
        $this->assertIsIterable($lines);
    }

    public function testException1()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid class: ``');
        new \Enum\Generator('', []);
    }

    public function testException2()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid property: ``');
        new \Enum\Generator('\\X', ['' => null]);
    }

    public function testException3()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid type: ``');
        new \Enum\Generator('\\X', ['A' => '']);
    }
}
