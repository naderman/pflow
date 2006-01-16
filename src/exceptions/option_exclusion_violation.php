<?php
/**
 * File containing the ezcConsoleOptionExclusionViolationException.
 * 
 * @package ConsoleTools
 * @version //autogen//
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * An exclusion rule for a parameter was violated.
 * This exception can be caught using {@link ezcConsoleOptionException}.
 *
 * @package ConsoleTools
 * @version //autogen//
 */
class ezcConsoleOptionExclusionViolationException extends ezcConsoleOptionException
{
    function __construct( ezcConsoleOption $excludingOption, ezcConsoleOption $excludedOption, $value = null )
    {
        $message = "The option <{$excludingOption->long}> excludes the option <{$excludedOption->long}>, but this was submitted.";
        if ( $value !== null )
        {
            $message .= "having the value <{$value}> ";
        }
        $message .= "but this one was submitted.";
        parent::__construct( $message );
    }
}
?>
