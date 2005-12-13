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
    protected $color = 'default';

    /**
     * Names of styles that are used for this format.
     * 
     * @var array(string)
     */
    protected $style = array( 'default' );

    /**
     * Name of the bgcolor that is used for this format.
     * 
     * @var string
     */
    protected $bgcolor = 'default';

    /**
     * Create a new ezcConsoleOutputFormat object.
     * Creates a new object of this class.
     * 
     * @param string $color        Name of a color value.
     * @param array(string) $style Names of style values.
     * @param string $bgcolor      Name of a bgcolor value.
     */
    public function __construct($color = 'default', array $style = null, $bgcolor = 'default')
    {
        $this->__set('color', $color);
        $this->__set('style', isset( $style ) ? $style : array( 'default' ) );
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
        if ( !isset( $this->$key ) )
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
            $this->style = $val;
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
        $this->$key = $val;
    }
    
}

?>
