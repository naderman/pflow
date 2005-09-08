<?php

require_once 'PHPUnit2/Framework/TestCase.php';

class ezcTestCase extends PHPUnit2_Framework_TestCase
{
    public function __construct( $string = "" )
    {
        parent::__construct( $string );
    }

    /**
     * Checks if $expectedValues are properly set on $propertyName in $object.
     */
    public function assertProperty( $object, $expectedValues, $propertyName )
    {
        foreach( $expectedValues as $value )
        {
            $object->$propertyName = $value;
            $this->assertEquals( $value, $object->$propertyName );
        }
    }

    /**
     * Checks if $setValues fail when set on $propertyName in $object.
     * Setting the property must result in an exception.
     */
    public function assertPropertyFails( $object, $setValues, $propertyName )
    {
        foreach( $setValues as $value )
        {
            try
            {
                $object->$propertyName = $value;
            }
            catch( Exception $e ){
                return;
            }
            $this->fail( "Setting property $propertyName to $value did not fail." );
        }
    }
}


?>
