<?php
/**
 * File containing the ezcConsoleOutputException class.
 *
 * @package ConsoleTools
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 * @filesource
 */

/**
 * General exception for use in {@see ezcConsoleOutput} class.
 * 
 * @package ConsoleTools
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 * @todo Error codes to be defined.
 */
class ezcConsoleOutputException extends Exception
{
    /**
     * Error code to indicate that a position should be restored, where
     * no position has been stored before.
     */
    const NO_POSITION_STORED = 1;
}
