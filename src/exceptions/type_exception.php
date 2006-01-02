<?php
/**
 * File containing the ezcBaseTypeException class.
 *
 * @package Base
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * ezcBaseTypeException is thrown whenever the type of the given instance is not 
 * as expected.
 *
 * @package Base
 * @version //autogen//
 */
class ezcBaseTypeException extends Exception
{
    /**
     * Constructs a new ezcBaseTypeException on the 
     */
    function __construct( $expectedType, $gotType )
    {
        parent::__construct( "Expected type <{$expectedType}> but got type <{$gotType}> for argument." );
    }
}
?>
