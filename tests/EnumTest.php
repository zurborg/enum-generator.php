<?php

use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
{
    public function testBooly()
    {
        $this->assertTrue(class_exists(Booly::class));

        $this->assertSame('Booly::False', Booly::False);
        $this->assertSame('Booly::True', Booly::True);

        $bool = Booly::False();
        $this->assertInstanceOf(Booly::class, $bool);
        $this->assertTrue($bool->isFalse());
        $this->assertFalse($bool->isTrue());
        $this->assertSame(Booly::False, "$bool");
        $this->assertEquals(Booly::False, $bool);
        $bool->setFalse();
        $this->assertTrue($bool->isFalse());
        $this->assertFalse($bool->isTrue());
        $this->assertSame(Booly::False, "$bool");
        $this->assertEquals(Booly::False, $bool);
        $this->assertSame(
            'OK',
            $bool->whenFalse(
                function () {
                    return 'OK';
                }
            )
        );
        $this->assertSame(
            'OK',
            $bool->whenTrue(
                function () {
                    $this->fail('MEH');
                },
                'OK'
            )
        );
        $bool->setTrue();
        $this->assertFalse($bool->isFalse());
        $this->assertTrue($bool->isTrue());
        $this->assertSame(Booly::True, "$bool");
        $this->assertEquals(Booly::True, $bool);
        $this->assertSame(
            'OK',
            $bool->whenFalse(
                function () {
                    $this->fail('MEH');
                },
                'OK'
            )
        );
        $this->assertSame(
            'OK',
            $bool->whenTrue(
                function () {
                    return 'OK';
                }
            )
        );
    }

    public function testPrimitive()
    {
        $this->assertTrue(class_exists(Primitive::class));

        $this->assertSame('Primitive::Void', Primitive::Void);
        $this->assertSame('Primitive::Bool', Primitive::Bool);
        $this->assertSame('Primitive::Int', Primitive::Int);
        $this->assertSame('Primitive::Float', Primitive::Float);
        $this->assertSame('Primitive::String', Primitive::String);
        $this->assertSame('Primitive::Array', Primitive::Array);

        $prim = Primitive::Void();
        $this->assertInstanceOf(Primitive::class, $prim);

        $this->assertTrue($prim->isVoid());
        $this->assertFalse($prim->isBool());
        $this->assertFalse($prim->isInt());
        $this->assertFalse($prim->isFloat());
        $this->assertFalse($prim->isString());
        $this->assertFalse($prim->isArray());
        $this->assertFalse($prim->isInt());
        $this->assertEquals(Primitive::Void, $prim);

        $this->assertNull($prim->getBool());
        $this->assertTrue($prim->getBool(true));
        $this->assertFalse($prim->getBool(false));

        $this->assertNull($prim->getInt());
        $this->assertSame(0, $prim->getInt(0));
        $this->assertSame(1, $prim->getInt(1));

        $this->assertNull($prim->getFloat());
        $this->assertSame(0.0, $prim->getFloat(0.0));
        $this->assertSame(1.0, $prim->getFloat(1.0));

        $this->assertNull($prim->getString());
        $this->assertSame('', $prim->getString(''));
        $this->assertSame('Hello, World!', $prim->getString('Hello, World!'));

        $this->assertNull($prim->getArray());
        $this->assertSame([], $prim->getArray([]));
        $this->assertSame(
            [null, true, false, 0, 1, 0.0, 1.0, '', 'Hello, World!', []],
            $prim->getArray([null, true, false, 0, 1, 0.0, 1.0, '', 'Hello, World!', []])
        );

        $this->assertSame(
            'OK',
            $prim->whenVoid(
                function () {
                    return 'OK';
                }
            )
        );

        $prim->setBool(false);
        $this->assertEquals(Primitive::Bool, $prim);
        $this->assertFalse($prim->isVoid());
        $this->assertTrue($prim->isBool());
        $this->assertFalse($prim->isInt());
        $this->assertFalse($prim->isFloat());
        $this->assertFalse($prim->isString());
        $this->assertFalse($prim->isArray());
        $this->assertFalse($prim->isInt());
        $this->assertNull($prim->getInt());
        $this->assertNull($prim->getFloat());
        $this->assertNull($prim->getString());
        $this->assertNull($prim->getArray());
        $this->assertFalse($prim->getBool());
        $this->assertFalse($prim->getBool(true));
        $this->assertFalse($prim->getBool(false));
        $this->assertSame(
            false,
            $prim->whenBool(
                function (bool $value) {
                    return $value;
                }
            )
        );

        $prim->setBool(true);
        $this->assertEquals(Primitive::Bool, $prim);
        $this->assertFalse($prim->isVoid());
        $this->assertTrue($prim->isBool());
        $this->assertFalse($prim->isInt());
        $this->assertFalse($prim->isFloat());
        $this->assertFalse($prim->isString());
        $this->assertFalse($prim->isArray());
        $this->assertFalse($prim->isInt());
        $this->assertNull($prim->getInt());
        $this->assertNull($prim->getFloat());
        $this->assertNull($prim->getString());
        $this->assertNull($prim->getArray());
        $this->assertTrue($prim->getBool());
        $this->assertTrue($prim->getBool(true));
        $this->assertTrue($prim->getBool(false));
        $this->assertSame(
            true,
            $prim->whenBool(
                function (bool $value) {
                    return $value;
                }
            )
        );

        $prim->setInt(123);
        $this->assertEquals(Primitive::Int, $prim);
        $this->assertFalse($prim->isVoid());
        $this->assertFalse($prim->isBool());
        $this->assertTrue($prim->isInt());
        $this->assertFalse($prim->isFloat());
        $this->assertFalse($prim->isString());
        $this->assertFalse($prim->isArray());
        $this->assertNull($prim->getBool());
        $this->assertNull($prim->getFloat());
        $this->assertNull($prim->getString());
        $this->assertNull($prim->getArray());
        $this->assertSame(123, $prim->getInt());
        $this->assertSame(123, $prim->getInt(123));
        $this->assertSame(123, $prim->getInt(456));
        $this->assertSame(
            123,
            $prim->whenInt(
                function (int $value) {
                    return $value;
                }
            )
        );

        $prim->setFloat(123.456);
        $this->assertEquals(Primitive::Float, $prim);
        $this->assertFalse($prim->isVoid());
        $this->assertFalse($prim->isBool());
        $this->assertFalse($prim->isInt());
        $this->assertTrue($prim->isFloat());
        $this->assertFalse($prim->isString());
        $this->assertFalse($prim->isArray());
        $this->assertNull($prim->getBool());
        $this->assertNull($prim->getInt());
        $this->assertNull($prim->getString());
        $this->assertNull($prim->getArray());
        $this->assertSame(123.456, $prim->getFloat());
        $this->assertSame(123.456, $prim->getFloat(123.456));
        $this->assertSame(123.456, $prim->getFloat(456.789));
        $this->assertSame(
            123.456,
            $prim->whenFloat(
                function (float $value) {
                    return $value;
                }
            )
        );

        $prim->setString('Hello, World!');
        $this->assertEquals(Primitive::String, $prim);
        $this->assertFalse($prim->isVoid());
        $this->assertFalse($prim->isBool());
        $this->assertFalse($prim->isInt());
        $this->assertFalse($prim->isFloat());
        $this->assertTrue($prim->isString());
        $this->assertFalse($prim->isArray());
        $this->assertNull($prim->getBool());
        $this->assertNull($prim->getInt());
        $this->assertNull($prim->getFloat());
        $this->assertNull($prim->getArray());
        $this->assertSame('Hello, World!', $prim->getString());
        $this->assertSame('Hello, World!', $prim->getString('Hello, World!'));
        $this->assertSame('Hello, World!', $prim->getString('World says Hello'));
        $this->assertSame(
            'Hello, World!',
            $prim->whenString(
                function (string $value) {
                    return $value;
                }
            )
        );

        $prim->setArray([1, 2, 3]);
        $this->assertEquals(Primitive::Array, $prim);
        $this->assertFalse($prim->isVoid());
        $this->assertFalse($prim->isBool());
        $this->assertFalse($prim->isInt());
        $this->assertFalse($prim->isFloat());
        $this->assertFalse($prim->isString());
        $this->assertTrue($prim->isArray());
        $this->assertNull($prim->getBool());
        $this->assertNull($prim->getInt());
        $this->assertNull($prim->getFloat());
        $this->assertNull($prim->getString());
        $this->assertSame([1, 2, 3], $prim->getArray());
        $this->assertSame([1, 2, 3], $prim->getArray([1, 2, 3]));
        $this->assertSame([1, 2, 3], $prim->getArray([4, 5, 6]));
        $this->assertSame(
            [1, 2, 3],
            $prim->whenArray(
                function (array $value) {
                    return $value;
                }
            )
        );
    }

    public function testAppFoo()
    {
        $A = \App\Foo::A();
        $B = \App\Foo::B();
        $C = \App\Foo::C();

        $this->assertInstanceOf(\App\Foo::class, $A);
        $this->assertInstanceOf(\App\Foo::class, $B);
        $this->assertInstanceOf(\App\Foo::class, $C);

        $this->assertNotSame($A, $B);
        $this->assertNotSame($A, $C);

        $this->assertNotSame($B, $A);
        $this->assertNotSame($B, $C);

        $this->assertNotSame($C, $B);
        $this->assertNotSame($C, $A);

        $this->assertTrue($A->isA());
        $this->assertTrue($B->isB());
        $this->assertTrue($C->isC());

        $this->assertFalse($A->isB());
        $this->assertFalse($A->isC());

        $this->assertFalse($B->isA());
        $this->assertFalse($B->isC());

        $this->assertFalse($C->isA());
        $this->assertFalse($C->isB());

        $this->assertSame("App\\Foo::A", "$A");
        $this->assertSame("App\\Foo::B", "$B");
        $this->assertSame("App\\Foo::C", "$C");
    }

    public function testAppFooBar()
    {
        $A = \App\Foo\Bar::A(1);
        $B = \App\Foo\Bar::B(2);
        $C = \App\Foo\Bar::C(3);

        $this->assertInstanceOf(\App\Foo\Bar::class, $A);
        $this->assertInstanceOf(\App\Foo\Bar::class, $B);
        $this->assertInstanceOf(\App\Foo\Bar::class, $C);

        $this->assertTrue($A->isA());
        $this->assertTrue($B->isB());
        $this->assertTrue($C->isC());

        $this->assertFalse($A->isB());
        $this->assertFalse($A->isC());

        $this->assertFalse($B->isA());
        $this->assertFalse($B->isC());

        $this->assertFalse($C->isA());
        $this->assertFalse($C->isB());

        $this->assertSame(1, $A->getA());
        $this->assertSame(2, $B->getB());
        $this->assertSame(3, $C->getC());

        $this->assertNull($A->getB());
        $this->assertNull($A->getC());

        $this->assertNull($B->getA());
        $this->assertNull($B->getC());

        $this->assertNull($C->getA());
        $this->assertNull($C->getB());

        $this->assertSame("App\\Foo\\Bar::A", "$A");
        $this->assertSame("App\\Foo\\Bar::B", "$B");
        $this->assertSame("App\\Foo\\Bar::C", "$C");
    }

    public function testIdentity()
    {
        $A1 = \App\Foo::A();
        $A2 = \App\Foo::A();

        $this->assertNotSame($A1, $A2);
        $this->assertEquals($A1, $A2);

        $this->assertSame(\App\Foo::A, "$A1");
        $this->assertNotSame(\App\Foo::A, $A1);
        $this->assertEquals(\App\Foo::A, $A1);
        $this->assertNotEquals(\App\Foo::B, $A1);

        $this->assertTrue($A1 == \App\Foo::A);
        $this->assertFalse($A1 == \App\Foo::B);
        $this->assertFalse($A1 === \App\Foo::A);
        $this->assertFalse($A1 === \App\Foo::B);

        $A = \App\Foo::A();
        $B = \App\Foo\Bar::A(0);

        $this->assertNotSame($A, $B);
        $this->assertNotEquals($A, $B);
    }

    public function testEq()
    {
        $A1 = \App\Foo::A();
        $A2 = \App\Foo::A();

        $this->assertTrue($A1->eq($A1));
        $this->assertTrue($A2->eq($A2));

        $this->assertTrue($A1->eq($A2));
        $this->assertTrue($A2->eq($A1));

        $B = \App\Foo::B();
        $this->assertFalse($A1->eq($B));
        $this->assertFalse($B->eq($A1));

        $void = Primitive::Void();
        $str1 = Primitive::String('foo');
        $str2 = Primitive::String('bar');
        $str3 = Primitive::String('foo');

        $this->assertFalse($void->eq($str1));
        $this->assertFalse($void->eq($str2));
        $this->assertFalse($void->eq($str3));

        $this->assertFalse($str1->eq($str2));
        $this->assertFalse($str2->eq($str3));

        $this->assertTrue($str1->eq($str1));
        $this->assertTrue($str3->eq($str3));

        $this->assertNotSame($str1, $str3);

        $this->assertTrue($str3->eq($str1));
        $this->assertTrue($str1->eq($str3));
    }

    public function testChain()
    {
        $foo = \App\Foo::A();
        $bar = \App\Foo\Bar::Parent($foo);
        $this->assertEquals(\App\Foo::A, $foo);
        $bar->whenParent(function (\App\Foo $value) { $value->setB(); });
        $this->assertEquals(\App\Foo::B, $foo);
        $bar->getParent()->setC();
        $this->assertEquals(\App\Foo::C, $foo);
    }

    public function testMutate()
    {
        $foo = \App\Foo::A();
        $this->assertTrue($foo->canMutate());
        $foo->freeze();
        $this->assertFalse($foo->canMutate());
        $bar = clone $foo;
        $this->assertSame("$foo", "$bar");
        $this->assertFalse($foo->canMutate());
        $this->assertTrue($bar->canMutate());
    }
}
