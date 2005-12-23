<?php
/**
 * File containing the ezcConsoleOutputException class.
 *
 * @package ConsoleTools
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * General exception for use in {@link ezcConsoleOutput} class.
 * 
 * @package ConsoleTools
 * @version //autogen//
 */
class ezcConsoleOutputException extends Exception
{
    /**
     * Error code to indicate that a position should be restored, where
     * no position has been stored before.
     */
    const NO_POSITION_STORED = 1;
}
?>
