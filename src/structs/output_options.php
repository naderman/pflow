<?php
/**
 * File containing the ezcConsoleOutputOptions class.
 *
 * @package ConsoleTools
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Struct class to store the options of the ezcConsoleOutput class.
 *
 * This class stores the options for the {@link ezcConsoleOutput} class.
 * 
 * @package ConsoleTools
 * @version //autogen//
 */
class ezcConsoleOutputOptions {

    /**
     * Determines the level of verbosity. 
     * 
     * @var int
     */
    public $verboseLevel = 1;

    /**
     * Determins, whether text is automatically wrapped after a specific amount
     * of characters in a line. If set to 0 (default), lines will not be wrapped
     * automatically.
     * 
     * @var int
     */
    public $autobreak = 0;

    /**
     * Wether to use formatings or not. 
     * 
     * @var bool
     */
    public $useFormats = true;

    /**
     * Create a new ezcConsoleOutputOptions struct. 
     * Create a new ezcConsoleOutputOptions struct for use with {@link ezcConsoleOutput}. 
     * 
     * @param int $verboseLevel Verbosity of the output to show.
     * @param int $autobreak    Auto wrap lines after num chars (0 = unlimited)
     * @param bool $useFormats  Whether to enable formated output
     * @return void
     */
    public function __construct( $verboseLevel = 1, $autobreak = 0, $useFormats = true )
    {
        $this->verboseLevel = $verboseLevel;
        $this->autobreak = $autobreak;
        $this->useFormats = $useFormats;
    }

}

?>
