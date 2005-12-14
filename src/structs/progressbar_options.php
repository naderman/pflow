<?php
/**
 * File containing the ezcConsoleProgressbarOptions class.
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
class ezcConsoleProgressbarOptions {

    /**
     * Char to fill progress bar with when the bar grows.
     *
     * @var string
     */
    protected $barChar = '+';
    
    /**
     * Char to fill empty space in progress bar with.
     *
     * @var string
     */
    protected $emptyChar = '-';     
    
    /**
     * Right most char of the progress bar filling (indicating the progress level).
     *
     * @var string
     */
    protected $progressChar = '>';     
    
    /**
     * sprintf() like format string, representing the complete progressbar area.
     *
     * @var string
     */
    protected $formatString = '%act% / %max% [%bar%] %fraction%%';
    
    /**
     * Maximum width of the progressbar area in characters.
     *
     * @var int
     */
    protected $width = 100;
    
    /**
     * sprintf() like format string for the fraction to display.
     *
     * @var string
     */
    protected $fractionFormat = '%01.2f';

    /**
     * Create a new ezcConsoleProgressbarOptions struct. 
     * Create a new ezcConsoleProgressbarOptions struct for use with {@link ezcConsoleOutput}. 
     * 
     * @param int $verboseLevel Verbosity of the output to show.
     * @param int $autobreak    Auto wrap lines after num chars (0 = unlimited)
     * @param bool $useFormats  Whether to enable formated output
     */
    public function __construct( 
        $barChar = '+',
        $emptyChar = '-', 
        $progressChar = '>',
        $formatString = '%act% / %max% [%bar%] %fraction%%',
        $width = 100,
        $fractionFormat = '%01.2f'
    )
    {
        $this->__set( 'barChar', $barChar );
        $this->__set( 'emptyChar', $emptyChar );
        $this->__set( 'progressChar', $progressChar );
        $this->__set( 'formatString', $formatString );
        $this->__set( 'width', $width );
        $this->__set( 'fractionFormat', $fractionFormat );
    }

    /**
     * Property read access.
     * 
     * @param string $key Name of the property.
     * @return mixed Value of the property or null.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If the the desired property is not found.
     */
    public function __get( $key )
    {
        if ( isset( $this->$key ) )
        {
            return $this->$key;
        }
        throw new ezcBasePropertyNotFoundException( $key );
    }

    /**
     * Property write access.
     * 
     * @param string $key Name of the property.
     * @param mixed $val  The value for the property.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If a desired property could not be found.
     * @throws ezcBaseConfigException
     *         If a desired property value is out of range
     *         {@link ezcBaseConfigException::VALUE_OUT_OF_RANGE}.
     */
    public function __set( $key, $val )
    {
        switch ( $key )
        {
            case 'barChar':
            case 'emptyChar':
            case 'progressChar':
            case 'formatString':
            case 'fractionFormat':
                if ( strlen( $val ) < 1 )
                {
                    throw new ezcBaseConfigException( $key, ezcBaseConfigException::VALUE_OUT_OF_RANGE, $val );
                }
                break;
            case 'width':
                if ( !is_int( $val ) || $val < 5 )
                {
                    throw new ezcBaseConfigException( $key, ezcBaseConfigException::VALUE_OUT_OF_RANGE, $val );
                }
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $key );
        }
        $this->$key = $val;
    }

}

?>
