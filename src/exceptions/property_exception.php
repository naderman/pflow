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
 * ezcBasePropertyException is thrown whenever a property is tried to be set
 * with an illegal value.
 *
 * @package Base
 * @version //autogen//
 */
class ezcBasePropertyException extends Exception
{
    /**
     * Constructs a new ezcBasePropertyException for the property $name for the value $value. 
     * Optionally specify the $range you would have expected the parameter to be in.
     */
    function __construct( $name, $value, $range = null )
    {
        $rangePart = $range == NULL ? '' : " (expected <{$range}>)";
        parent::__construct( "The value <{$value}> is illegal for the property <{$name}>$rangePart.", 0 );
    }
}
?>
