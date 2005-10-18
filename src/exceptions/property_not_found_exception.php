<?php
/**
 * File containing the ezcPropertyNotFoundException class
 *
 * @package Base
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 */
/**
 * ezcPropertyNotFoundException is thrown whenever a non existent property
 * is accessed in the Components library.
 *
 * @package Base
 */
class ezcBasePropertyNotFoundException extends Exception
{
    /**
     * Constructs a new ezcPropertyNotFoundException on the property
     * $name.
     */
    function __construct( $name )
    {
        parent::__construct( "No such propertyname: $name", 0 );
    }
}
?>
