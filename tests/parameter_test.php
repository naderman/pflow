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

    /**
     * Expected parameters of certain methods or functions
     * @var array<string,ReflectionParameter[]>
     */
    protected $expected = array();

    /**
     * Actual parameters of the same methods or functions as in $this->expected
     * @var array<string,ezcReflectionParameter[]>
     */
    protected $actual = array();

    /**#@+
     * @var ezcReflectionParameter
     * @deprecated
     */
    protected $actualParamsOfM1;
    protected $actualParamsOf_TestMethods_m3;
    protected $actualParamsOf_ezcReflectionApi_setReflectionTypeFactory;
    protected $actualParamsOf_functionWithTypeHint;
    /**#@-*/

    public function setUp() {
        // function with three parameters that have type annotations but no type hints
        //$this->expectedFunctionM1 = new ReflectionFunction( 'm1' );
        //$this->expectedParamsOfM1 = $this->expectedFunctionM1->getParameters();

        // method with one undocumented parameter
        //$this->expectedMethod_TestMethods_m3 = new ReflectionMethod( 'TestMethods', 'm3' );
        //$this->expectedParamsOfMethod_TestMethods_m3 = $this->expectedMethod_TestMethods_m3->getParameters();

        $this->setUpFixtures();
        $this->actual['m1'] = $this->actualParamsOfM1;
        $this->actual['TestMethods::m3'] = $this->actualParamsOf_TestMethods_m3;
        $this->actual['ezcReflectionApi::setReflectionTypeFactory'] = $this->actualParamsOf_ezcReflectionApi_setReflectionTypeFactory;
        $this->actual['functionWithTypeHint'] = $this->actualParamsOf_functionWithTypeHint;
    }

    public function setUpFixtures() {
        // function with undocumented parameter $t that has default value 'foo'
        $this->actual['mmm'][0] = new ezcReflectionParameter( 'mmm', 0 );

        // function with three parameters that have type annotations but no type hints
        //$this->actualFunctionM1   = new ezcReflectionFunction( 'm1' );
        $this->actualParamsOfM1[]   = new ezcReflectionParameter( 'm1', 0 );
        $this->actualParamsOfM1[]   = new ezcReflectionParameter( 'm1', 1 );
        $this->actualParamsOfM1[]   = new ezcReflectionParameter( 'm1', 2 );

        // method with one undocumented parameter
        //$this->actualMethod_TestMethods_m3   = new ezcReflectionMethod( 'TestMethods', 'm3' );
        $this->actualParamsOf_TestMethods_m3[] = new ezcReflectionParameter( array( 'TestMethods', 'm3' ), 0 );

        // method with parameter that has type hint
        //$this->actualMethod_ezcReflectionApi_setReflectionTypeFactory
        //    = new ezcReflectionMethod( 'ezcReflectionApi', 'setReflectionTypeFactory' );
        $this->actualParamsOf_ezcReflectionApi_setReflectionTypeFactory[]
            = new ezcReflectionParameter( array( 'ezcReflectionApi', 'setReflectionTypeFactory' ), 0 );

        // function with parameter that has type hint only
        //$this->actualFunction_functionWithTypeHint = new ezcReflectionFunction( 'functionWithTypeHint' );
        $this->actualParamsOf_functionWithTypeHint[] = new ezcReflectionParameter( 'functionWithTypeHint', 0 );
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
        $params = $this->actualParamsOfM1;

		$decFunc = $params[0]->getDeclaringFunction();
		self::assertTrue($decFunc instanceof ezcReflectionFunction);
        self::assertEquals('m1', $decFunc->getName());

        $decFunc = $this->actual['TestMethods::m3'][0]->getDeclaringFunction();
        self::assertType('ezcReflectionMethod', $decFunc);
        self::assertEquals('TestMethods', $decFunc->getDeclaringClass()->getName());
        self::assertEquals('m3', $decFunc->getName());
        
        $decFunc = $this->actual['ezcReflectionApi::setReflectionTypeFactory'][0]->getDeclaringFunction();
        self::assertType('ezcReflectionMethod', $decFunc);
        self::assertEquals('ezcReflectionApi', $decFunc->getDeclaringClass()->getName());
        self::assertEquals('setReflectionTypeFactory', $decFunc->getName());

        $decFunc = $this->actual['functionWithTypeHint'][0]->getDeclaringFunction();
        self::assertType('ezcReflectionFunction', $decFunc);
        self::assertEquals('functionWithTypeHint', $decFunc->getName());
    }

    public function testGetDeclaringClass() {
        $params = $this->actualParamsOf_TestMethods_m3;

        $class = $params[0]->getDeclaringClass();
		self::assertTrue($class instanceof ezcReflectionClass);
        self::assertEquals('TestMethods', $class->getName());

        self::assertNull( $this->actual['mmm'][0]->getDeclaringClass() );
    }

    public function testGetName() {
        self::assertEquals('test', $this->actualParamsOfM1[0]->getName());
        self::assertEquals('test2', $this->actualParamsOfM1[1]->getName());
        self::assertEquals('test3', $this->actualParamsOfM1[2]->getName());
	}

    public function testIsPassedByReference() {
        $params = $this->actualParamsOfM1;
		self::assertFalse($params[0]->isPassedByReference());
		self::assertTrue($params[2]->isPassedByReference());
	}

    public function testIsArray() {
        $params = $this->actualParamsOfM1;
		self::assertFalse($params[0]->isArray());
	}

    public function testAllowsNull() {
        $params = $this->actualParamsOfM1;
		self::assertTrue($params[0]->allowsNull());
	}

    public function testIsOptional() {
		$param = $this->actual['mmm'][0];
		self::assertTrue($param->isOptional());

        $params = $this->actualParamsOfM1;
		$param = $params[0];
		self::assertFalse($param->isOptional());
	}

	public function testIsDefaultValueAvailable() {
		$param = $this->actual['mmm'][0];
		self::assertTrue($param->isDefaultValueAvailable());

        $params = $this->actualParamsOfM1;
		$param = $params[0];
		self::assertFalse($param->isDefaultValueAvailable());
	}

	public function testGetDefaultValue() {
		$param = $this->actual['mmm'][0];
		self::assertEquals('foo', $param->getDefaultValue());
	}

	/**
     * @expectedException ReflectionException
     */
	public function testGetDefaultValueThrowsReflectionException() {
        $params = $this->actualParamsOfM1;
		$param = $params[0];
		self::assertEquals(null, $param->getDefaultValue()); //should throw exception
	}

	public function testGetPosition() {
		$param = $this->actual['mmm'][0];
		self::assertEquals(0, $param->getPosition());

        $params = $this->actualParamsOfM1;
		$param = $params[1];
		self::assertEquals(1, $param->getPosition());
	}

    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( "ezcReflectionParameterTest" );
    }
}
?>
