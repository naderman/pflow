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
     * Storage for formating information. 
     * 
     * @var array(int => ezcConsoleOutputFormat)
     */
    protected $formats = array();
}

?>
