<?php
/**
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogen//
 * @filesource
 * @package Reflection
 * @subpackage Tests
 */

class ezcReflectionPropertiesFromClassTest extends ezcReflectionPropertyTest
{

    public function setUpFixtures()
    {
        $class = new ezcReflectionClass( 'SomeClass' );
        foreach ( $class->getProperties() as $property ) {
            if ( $property->getName() == $this->refPropName ) {
		        $this->refProp = $property;
            }
            elseif ( $property->getName() == $this->publicPropertyName ) {
                $this->publicProperty = $property;
            }
        }
    }

    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( "ezcReflectionPropertiesFromClassTest" );
    }
}
