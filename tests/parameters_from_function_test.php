<?php
/**
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogen//
 * @filesource
 * @package Reflection
 * @subpackage Tests
 */

class ezcReflectionParametersFromFunctionTest extends ezcReflectionParameterTest
{
    public function setUpFixtures() {
        // function with undocumented parameter $t that has default value 'foo'
        $function = new ezcReflectionFunction( 'mmm' );
        $this->actual['mmm'] = $function->getParameters();

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

    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( "ezcReflectionParametersFromFunctionTest" );
    }
}
?>
