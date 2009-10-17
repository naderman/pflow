<?php
/**
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogen//
 * @filesource
 * @package Reflection
 * @subpackage Tests
 */

class ezcReflectionParameterTest extends ezcTestCase
{
    public function setUp() {
        // function with three parameters that have type annotations but no type hints
        //$this->expectedFunctionM1 = new ReflectionFunction( 'm1' );
        //$this->expectedParamsOfM1 = $this->expectedFunctionM1->getParameters();
        $this->actualFunctionM1   = new ezcReflectionFunction( 'm1' );
        $this->actualParamsOfM1   = $this->actualFunctionM1->getParameters();

        // method with one undocumented parameter
        //$this->expectedMethod_TestMethods_m3 = new ReflectionMethod( 'TestMethods', 'm3' );
        //$this->expectedParamsOfMethod_TestMethods_m3 = $this->expectedMethod_TestMethods_m3->getParameters();
        $this->actualMethod_TestMethods_m3   = new ezcReflectionMethod( 'TestMethods', 'm3' );
        $this->actualParamsOf_TestMethods_m3 = $this->actualMethod_TestMethods_m3->getParameters();

        // method with parameter that has type hint
        $this->actualMethod_ezcReflectionApi_setReflectionTypeFactory
            = new ezcReflectionMethod( 'ezcReflectionApi', 'setReflectionTypeFactory' );
        $this->actualParamsOf_ezcReflectionApi_setReflectionTypeFactory
            = $this->actualMethod_ezcReflectionApi_setReflectionTypeFactory->getParameters();

        // function with parameter that has type hint only
        $this->actualFunction_functionWithTypeHint = new ezcReflectionFunction( 'functionWithTypeHint' );
        $this->actualParamsOf_functionWithTypeHint = $this->actualFunction_functionWithTypeHint->getParameters();
    }

    public function testGetType() {
        $type = $this->actualParamsOfM1[0]->getType();
        self::assertType('ezcReflectionType', $type);
        self::assertEquals('string', $type->toString());

        $type = $this->actualParamsOfM1[1]->getType();
        self::assertType('ezcReflectionType', $type);
        self::assertEquals('ezcReflectionApi', $type->toString());

        $type = $this->actualParamsOfM1[2]->getType();
        self::assertType('ezcReflectionType', $type);
        self::assertEquals('ReflectionClass', $type->toString());

        // this method has both a type hint and a type annotation
        $type = $this->actualParamsOf_ezcReflectionApi_setReflectionTypeFactory[0]->getType();
        self::assertType('ezcReflectionType', $type);
        self::assertEquals('ezcReflectionTypeFactory', $type->toString());

        // testing a param that only has a type hint
        $type = $this->actualParamsOf_functionWithTypeHint[0]->getType();
        self::assertType('ezcReflectionType', $type);
        self::assertEquals('ReflectionClass', $type->toString());

        self::assertNull($this->actualParamsOf_TestMethods_m3[0]->getType());
    }

    public function testGetClass() {
        self::assertNull( $this->actualParamsOfM1[0]->getClass() );
        self::assertNull( $this->actualParamsOfM1[1]->getClass() );
        self::assertNull( $this->actualParamsOfM1[2]->getClass() );
        self::assertNull( $this->actualParamsOf_TestMethods_m3[0]->getClass() );
        self::assertEquals( 'ezcReflectionTypeFactory',
            $this->actualParamsOf_ezcReflectionApi_setReflectionTypeFactory[0]->getClass()->getName() );
    }

    public function testGetDeclaringFunction() {
        $func = new ezcReflectionFunction('m1');
        $params = $func->getParameters();

		$decFunc = $params[0]->getDeclaringFunction();
		self::assertTrue($decFunc instanceof ezcReflectionFunction);
        self::assertEquals('m1', $decFunc->getName());
    }

    public function testGetDeclaringClass() {
        $method = new ezcReflectionMethod('TestMethods', 'm3');
        $params = $method->getParameters();

        $class = $params[0]->getDeclaringClass();
		self::assertTrue($class instanceof ezcReflectionClass);
        self::assertEquals('TestMethods', $class->getName());
    }

    public function testGetName() {
        self::assertEquals('test', $this->actualParamsOfM1[0]->getName());
        self::assertEquals('test2', $this->actualParamsOfM1[1]->getName());
        self::assertEquals('test3', $this->actualParamsOfM1[2]->getName());
	}

    public function testIsPassedByReference() {
		$func = new ezcReflectionFunction('m1');
        $params = $func->getParameters();
		self::assertFalse($params[0]->isPassedByReference());
		self::assertTrue($params[2]->isPassedByReference());
	}

    public function testIsArray() {
		$func = new ezcReflectionFunction('m1');
        $params = $func->getParameters();
		self::assertFalse($params[0]->isArray());
	}

    public function testAllowsNull() {
		$func = new ezcReflectionFunction('m1');
        $params = $func->getParameters();
		self::assertTrue($params[0]->allowsNull());
	}

    public function testIsOptional() {
		$func = new ezcReflectionFunction('mmm');
		$param = $func->getParameters();
		$param = $param[0];
		self::assertTrue($param->isOptional());

		$func = new ezcReflectionFunction('m1');
		$param = $func->getParameters();
		$param = $param[0];
		self::assertFalse($param->isOptional());
	}

	public function testIsDefaultValueAvailable() {
		$func = new ezcReflectionFunction('mmm');
		$param = $func->getParameters();
		$param = $param[0];
		self::assertTrue($param->isDefaultValueAvailable());

		$func = new ezcReflectionFunction('m1');
		$param = $func->getParameters();
		$param = $param[0];
		self::assertFalse($param->isDefaultValueAvailable());
	}

	/**
	* @expectedException ReflectionException
	*/
	public function testGetDefaultValue() {
		$func = new ezcReflectionFunction('mmm');
		$param = $func->getParameters();
		$param = $param[0];
		self::assertEquals('foo', $param->getDefaultValue());

		$func = new ezcReflectionFunction('m1');
		$param = $func->getParameters();
		$param = $param[0];
		self::assertEquals(null, $param->getDefaultValue()); //should throw exception
	}

	public function testGetPosition() {
		$func = new ezcReflectionFunction('mmm');
		$param = $func->getParameters();
		$param = $param[0];
		self::assertEquals(0, $param->getPosition());

		$func = new ezcReflectionFunction('m1');
		$param = $func->getParameters();
		$param = $param[1];
		self::assertEquals(1, $param->getPosition());
	}

    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( "ezcReflectionParameterTest" );
    }
}
?>
