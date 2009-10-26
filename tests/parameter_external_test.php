<?php
/**
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogen//
 * @filesource
 * @package Reflection
 * @subpackage Tests
 */

class ezcReflectionParameterExternalTest extends ezcReflectionParameterTest
{
    public function setUpFixtures() {
        // function with undocumented parameter $t that has default value 'foo'
        $function = new ReflectionFunction( 'mmm' );
        $this->expected['mmm'] = $function->getParameters();
        foreach ( $this->expected['mmm'] as $key => $param ) {
            $this->actual['mmm'][$key] = new ezcReflectionParameter( null, $param );
        }

        // function with three parameters that have type annotations but no type hints
        $this->expectedFunctionM1 = new ReflectionFunction( 'm1' );
        $this->expectedParamsOfM1 = $this->expectedFunctionM1->getParameters();
        $paramTypes = array( 'string', 'ezcReflectionApi', 'ReflectionClass' );
        foreach ( $this->expectedParamsOfM1 as $key => $param ) {
            $this->actualParamsOfM1[] = new ezcReflectionParameter( $paramTypes[$key], $param );
        }

        // method with one undocumented parameter
        $this->expectedMethod_TestMethods_m3 = new ReflectionMethod( 'TestMethods', 'm3' );
        $this->expectedParamsOfMethod_TestMethods_m3 = $this->expectedMethod_TestMethods_m3->getParameters();
        foreach ( $this->expectedParamsOfMethod_TestMethods_m3 as $param ) {
            $this->actualParamsOf_TestMethods_m3[] = new ezcReflectionParameter( null, $param );
        }

        // method with parameter that has type hint
        $this->expectedMethod_ezcReflectionApi_setReflectionTypeFactory
            = new ReflectionMethod( 'ezcReflectionApi', 'setReflectionTypeFactory' );
        $this->expectedParamsOf_ezcReflectionApi_setReflectionTypeFactory
            = $this->expectedMethod_ezcReflectionApi_setReflectionTypeFactory->getParameters();
        foreach ( $this->expectedParamsOf_ezcReflectionApi_setReflectionTypeFactory as $param ) {
            $this->actualParamsOf_ezcReflectionApi_setReflectionTypeFactory[] = new ezcReflectionParameter( 'ezcReflectionTypeFactory', $param );
        }

        // function with parameter that has type hint only
        $this->expectedFunction_functionWithTypeHint = new ReflectionFunction( 'functionWithTypeHint' );
        $this->expectedParamsOf_functionWithTypeHint = $this->expectedFunction_functionWithTypeHint->getParameters();
        $this->actualParamsOf_functionWithTypeHint[] = new ezcReflectionParameter( 'ReflectionClass', $this->expectedParamsOf_functionWithTypeHint[0] );
    }

    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( "ezcReflectionParameterExternalTest" );
    }

}
