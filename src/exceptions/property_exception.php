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
     * Constructs a new ezcPropertyException on the property $name for the value $value. 
     * Optionally specify the $range you would have expected the parameter to be in.
     */
    function __construct( $name, $value, $range = 'unknown' )
    {
        parent::__construct( "The value <$value> is ilegal for the property <{$name}> (expected <>).", 0 );
    }
}
?>
