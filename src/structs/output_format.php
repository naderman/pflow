<?php
/**
 * File containing the ezcConsoleOutputFormat class.
 *
 * @package ConsoleTools
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Struct class to store formating entities used by ezcConsoleOutput.
 *
 * Struct class to store formating entities used by ezcConsoleOutput.
 * 
 * @package ConsoleTools
 * @version //autogen//
 */
class ezcConsoleOutputFormat {

    /**
     * Code of the color that is used for this format.
     * 
     * @var int
     */
    protected $color = 0;

    /**
     * Code of the style that is used for this format.
     * 
     * @var int
     */
    protected $style = 0;

    /**
     * Code of the bgcolor that is used for this format.
     * 
     * @var int
     */
    protected $bgcolor = 0;

    /**
     * Stores the mapping of color names to their escape
     * sequence values.
     *
     * @var array(string => int)
     */
    protected static $colors = array(
    	'gray'          => 30,
    	'red'           => 31,
    	'green'         => 32,
    	'yellow'        => 33,
    	'blue'          => 34,
    	'magenta'       => 35,
    	'cyan'          => 36,
    	'white'         => 37,
        'default'       => 39
    );

    /**
     * Stores the mapping of bgcolor names to their escape
     * sequence values.
     * 
     * @var array(string => int)
     */
    protected static $bgcolors = array(
        'black'      => 40,
        'red'        => 41,
    	'green'      => 42,
    	'yellow'     => 43,
    	'blue'       => 44,
    	'magenta'    => 45,
    	'cyan'       => 46,
    	'white'      => 47,
        'default'    => 49,
    );

    /**
     * Stores the mapping of styles names to their escape
     * sequence values.
     * 
     * @var array(string => int)
     */
    protected static $styles = array( 
        'default'           => '0',
    
        'bold'              => 1,
        'faint'             => 2,
        'normal'            => 22,
        
        'italic'            => 3,
        'notitalic'         => 23,
        
        'underlined'        => 4,
        'doubleunderlined'  => 21,
        'notunderlined'     => 24,
        
        'blink'             => 5,
        'blinkfast'         => 6,
        'noblink'           => 25,
        
        'negative'          => 7,
        'positive'          => 27,
    );

    /**
     * Basic escape sequence string. Use sprintf() to insert escape codes.
     * 
     * @var string
     */
    private $escapeSequence = "\033[%sm";

    /**
     * Create a new ezcConsoleOutputStyle object.
     * Creates a new object of this class.
     * 
     * @param string $color   Name of a color value.
     * @param string $style   Name of a style value.
     * @param string $bgcolor Name of a bgcolor value.
     */
    public function __construct($color = 'default', $style = 'default', $bgcolor = 'default')
    {
        $this->__set('color', $color);
        $this->__set('style', $style);
        $this->__set('bgcolor', $bgcolor);
    }

    
    /**
     * Overloaded __get() method to gain read-only access to some attributes.
     * 
     * @param string $key Name of the property to read.
     * @return mixed Desired value if exists, otherwise null.
     */
    public function __get( $key )
    {
        if ( isset( $this->$key ) )
        {
            return $this->$key;
        }
    }

    /**
     * Overloaded __set() method to gain read-only access to some attributes 
     * and perform checks on setting others.
     * 
     * @param string $key Name of the attrinbute to access.
     * @param string $val The value to set.
     *
     * @throws ezcBaseConfigException
     *         If the setting you try to access does not exists
     *         {@link UNKNOWN_CONFIG_SETTING}
     * @throws ezcBaseConfigException
     *         If trying to set an invalid value for a setting.
     *         {@link VALUE_OUT_OF_RANGE}
     */
    public function __set( $key, $val )
    {
        $valid = false;
        switch ( strtolower( $key ) )
        {
            case 'color':
                $valid = ezcConsoleOutputFormat::getColorCode( $val );
                break;
            case 'style':
                $valid = ezcConsoleOutputFormat::getStyleCode( $val );
                break;
            case 'bgcolor':
                $valid = ezcConsoleOutputFormat::getBgcolorCode( $val );
                break;
            default:
                throw new ezcBaseConfigException( 
                    $key,
                    ezcBaseConfigException::UNKNOWN_CONFIG_SETTING,
                    $val
                );
                break;
        }
        if ( $valid === false )
        {
            throw new ezcBaseConfigException( 
                $key,
                ezcBaseConfigException::VALUE_OUT_OF_RANGE,
                $val
            );
        }
        $this->$key = $this->{$key.'s'}[$val];
    }

    /**
     * Returns the integer code of a given color name of false if name is invalid.
     * 
     * @param string $name The color name to lookup.
     * @return mixed Integer code on success, otherwise bool false.
     */
    public static function getColorCode( $name )
    {
        return isset( self::$colors[$name] ) ? self::$colors[$name] : false;
    }

    /**
     * Returns the integer code of a given style name of false if name is invalid.
     * 
     * @param string $name The style name to lookup.
     * @return mixed Integer code on success, otherwise bool false.
     */
    public static function getStyleCode( $name )
    {
        return isset( self::$styles[$name] ) ? self::$styles[$name] : false;
    }

    /**
     * Returns the integer code of a given bgcolor name of false if name is invalid.
     * 
     * @param string $name The bgcolor name to lookup.
     * @return mixed Integer code on success, otherwise bool false.
     */
    public static function getBgcolorCode( $name )
    {
        return isset( self::$bgcolors[$name] ) ? self::$bgcolors[$name] : false;
    }
    
}

?>
