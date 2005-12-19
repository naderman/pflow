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
     * Name of the color that is used for this format.
     * 
     * @var string
     */
    public $color = 'default';

    /**
     * Names of styles that are used for this format.
     * 
     * @var array(string)
     */
    public $style = array( 'default' );

    /**
     * Name of the bgcolor that is used for this format.
     * 
     * @var string
     */
    public $bgcolor = 'default';


    protected $properties = array( 
        'color'     => 'default',
        'style'     => 'default',
        'bgcolor'   => 'default',
    );

    /**
     * Create a new ezcConsoleOutputFormat object.
     * Creates a new object of this class.
     * 
     * @param string $color        Name of a color value.
     * @param array(string) $style Names of style values.
     * @param string $bgcolor      Name of a bgcolor value.
     */
    public function __construct( $color = 'default', array $style = null, $bgcolor = 'default' )
    {
        unset( $this->color );
        $this->__set( 'color', $color );
        unset( $this->style );
        $this->__set( 'style', isset( $style ) ? $style : array( 'default' ) );
        unset( $this->bgcolor );
        $this->__set( 'bgcolor', $bgcolor );
    }

    
    /**
     * Overloaded __get() method to gain read-only access to some attributes.
     * 
     * @param string $key Name of the property to read.
     * @return mixed Desired value if exists, otherwise null.
     */
    public function __get( $key )
    {
        if ( isset( $this->properties[$key] ) )
        {
            return $this->properties[$key];
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
        if ( !isset( $this->properties[$key] ) )
        {
            throw new ezcBaseConfigException( 
                $key,
                ezcBaseConfigException::UNKNOWN_CONFIG_SETTING,
                $val
            );
        }
        // Extry handling of multi styles
        if ( $key === 'style' )
        {
            if ( !is_array( $val ) ) $val = array( $val );
            foreach ( $val as $style )
            {
                if ( !ezcConsoleOutput::isValidFormatCode( $key, $style ) )
                {
                    throw new ezcBaseConfigException( 
                        $key,
                        ezcBaseConfigException::VALUE_OUT_OF_RANGE,
                        $style
                    );
                }
            }
            $this->properties['style'] = $val;
            return;
        }
        // Continue normal handling
        if ( !ezcConsoleOutput::isValidFormatCode( $key, $val ) )
        {
            throw new ezcBaseConfigException( 
                $key,
                ezcBaseConfigException::VALUE_OUT_OF_RANGE,
                $val
            );
        }
        $this->properties[$key] = $val;
    }
 
    /**
     * Property isset access.
     * 
     * @param string $key Name of the property.
     * @return bool True is the property is set, otherwise false.
     */
    public function __isset( $key )
    {
        return isset( $this->properties[$key] );
    }
    
}

?>
