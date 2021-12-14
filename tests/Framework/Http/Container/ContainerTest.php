<?php

namespace Tests\Framework\Http\Container;

use Framework\Http\Container\Container;
use Framework\Http\Container\ServiceNotFoundException;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testPrimitives()
    {
        $container = new Container();

        $container->set($name = 'name', $value = 5);
        self::assertEquals($value, $container->get($name));

        $container->set($name = 'name', $value = 'string');
        self::assertEquals($value, $container->get($name));

        $container->set($name = 'name', $value = ['array']);
        self::assertEquals($value, $container->get($name));

        $container->set($name = 'name', $value = new \stdClass());
        self::assertEquals($value, $container->get($name));
    }

    public function testCallback()
    {
        $container = new Container();

        $container->set($name = 'name', function() {
            return new \stdClass();
        });

        self::assertNotNull($value = $container->get($name));
        self::assertInstanceOf(\stdClass::class, $value);
    }

    public function testSinglton()
    {
        $container = new Container();

        $container->set($name = 'name', function() {
            return new \stdClass();
        });

        self::assertNotNull($value1 = $container->get($name));
        self::assertNotNull($value2 = $container->get($name));

        self::assertSame($value1, $value2);
    }

    public function testContainerPass()
    {
        $container = new Container();

        $container->set('param', $value = 1);
        $container->set($name = 'name', function(Container $container) {
            $object =  new \stdClass();
            $object->param = $container->get('param');

            return $object;
        });

        self::assertObjectHasAttribute('param', $object = $container->get($name));
        self::assertEquals($value, $object->param);
    }

    public function testAutoInstantiating()
    {
        $container = new Container();

        self::assertNotNull($object1 = $container->get(\stdClass::class));
        self::assertNotNull($object2 = $container->get(\stdClass::class));

        self::assertInstanceOf(\stdClass::class, $object1);
        self::assertInstanceOf(\stdClass::class, $object2);

        self::assertSame($object1, $object2);
    }

    public function testAutowiring()
    {
        $container = new Container();

        $outer = $container->get(Outer::class);

        self::assertNotNull($outer);
        self::assertInstanceOf(Outer::class, $outer);

        self::assertNotNull($middle = $outer->middle);
        self::assertInstanceOf(Middle::class, $middle);

        self::assertNotNull($inner = $middle->inner);
        self::assertInstanceOf(Inner::class, $inner);
    }

    public function testAutowiringDefault()
    {
        $container = new Container();

        $def = $container->get(WithArrayAndDefault::class);

        self::assertNotNull($def);
        self::assertInstanceOf(WithArrayAndDefault::class, $def);

        self::assertNotNull($inner = $def->inner);
        self::assertInstanceOf(Inner::class, $inner);

        self::assertEquals([] , $def->array);
        self::assertEquals(10, $def->def);
    }

    public function testNotFound()
    {
        $container = new Container();

        self::expectException(ServiceNotFoundException::class);
        $container->get('password');

    }
}

class Outer
{
    public $middle;

    public function __construct(Middle $middle)
    {
        $this->middle = $middle;
    }
}

class Middle
{
    public $inner;

    public function __construct(Inner $inner)
    {
        $this->inner = $inner;
    }
}

class Inner
{

}

class WithArrayAndDefault
{
    public $array;
    public $def;
    public $inner;

    public function __construct(Inner $inner, array $array, $def = 10)
    {
        $this->inner = $inner;
        $this->array = $array;
        $this->def = $def;
    }
}