<?php
/**
 * File containing the ezcConsoleOutputFormats class.
 *
 * @package ConsoleTools
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Struct class to store the collection for formating classes.
 *
 * This struct stores objects of {@link ezcConsoleOutputFormat}, which represents
 * a format option set for {@link ezcConsoleOutput}.
 * 
 * @package ConsoleTools
 * @version //autogen//
 */
class ezcConsoleOutputFormats {

    /**
     * Array of ezcConsoleOutputFormat.
     * 
     * @var array(ezcConsoleOutputFormat)
     */
    protected $formats = array();

    /**
     * Create a new ezcConsoleOutputFormats object.
     * Creates a new, empty object of this class.
     * 
     */
    public function __construct()
    {
        $this->formats['default'] = new ezcConsoleOutputFormat();
    }

    
    /**
     * Read access to the formats.
     * Formats are accessed directly like properties of this object. If a
     * format does not exist, it is created on the fly (using default values),
     * 
     * @param string $key Name of the format to read.
     * @return ezcConsoleOutputFormat The format.
     */
    public function __get( $key )
    {
        if ( !isset( $this->formats[$key] ) )
        {
            $this->formats[$key] = new ezcConsoleOutputFormat();
        }
        return $this->formats[$key];
    }

    /**
     * Write access to the formats.
     * Formats are accessed directly like properties of this object. If a
     * format does not exist, it is created on the fly (using default values),
     * 
     * @param string $key            Name of the format to set.
     * @param ezcConsoleOutputFormat The format defintion.
     */
    public function __set( $key, ezcConsoleOutputFormat $val )
    {
        $this->formats[$key] = $val;
    }

}

?>
