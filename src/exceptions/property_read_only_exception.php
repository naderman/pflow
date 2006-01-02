<?php
/**
 * File containing the ezcPropertyReadOnlyException class
 *
 * @package Base
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * ezcBasePropertyReadOnlyException is thrown whenever a read-only property
 * is accessed in the Components library.
 *
 * @package Base
 * @version //autogen//
 */
class ezcBasePropertyReadOnlyException extends Exception
{
    /**
     * Constructs a new ezcPropertyReadOnlyException for the property
     * $name.
     */
    function __construct( $name )
    {
        parent::__construct( "The property <{$name}> is read-only.", 0 );
    }
}
?>
