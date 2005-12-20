<?php
/**
 * File containing the ezcPropertyException class
 *
 * @package Base
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * ezcPropertyException is thrown whenever a property is tried to be set with
 * an iligal value Components library.
 *
 * @package Base
 */
class ezcBasePropertyException extends Exception
{
    /**
     * The value you tried to set for a property was illegal. 
     */
    const ILLEGAL_VALUE = 1;
    /**
     * The value you tried to set for a property was not inside the legal range for this property. 
     */
    const ILLIGAL_RANGE = 2;
    
    /**
     * Constructs a new ezcPropertyException on the property $name for the value $value. 
     * Optionally specify the $range you would have expected the parameter to be in.
     */
    function __construct( $name, $value, $range = null )
    {
        if ( $range === null )
        {
            parent::__construct( 
                "The value <$value> is ilegal for the property <{$name}>.",
                ezcPropertyException::ILIGAL_VALUE
            );
        }
        else
        {
            parent::__construct( 
                "The value <$value> is ilegal for the property <{$name}> because it is out of range <$range>.",
                ezcPropertyException::ILIGAL_RANGE
            );
        }
    }
}
?>
