<?php
/**
 * File containing the ezcConsoleOptionRule class.
 *
 * @package ConsoleTools
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Struct class to store a parameter rule.
 *
 * This struct stores relation rules between parameters. A relation consists of
 * a parameter that the relation refers to and optionally the value(s) the 
 * refered parameter may have assigned. Rules may be used for dependencies and 
 * exclusions between parameters.
 *
 * @see ezcConsoleOption
 * 
 * @package ConsoleTools
 * @version //autogen//
 */
class ezcConsoleOptionRule {

    /**
     * The parameter this rule refers to. 
     * 
     * @var ezcConsoleOption
     */
    protected $option;

    /**
     * Values the refered parameter may accept. 
     * 
     * @var array(string)
     */
    protected $values = array();

    /**
     * Create a new parameter rule.
     * Creates a new parameter rule. Per default the $values parameter
     * is an empty array, which determines, that the parameter may accept any
     * value. To indicate that a parameter may only have certain values,
     * place them inside tha $values array. For example to indicate a parameter
     * may have the values 'a', 'b' and 'c' use:
     * <code>
     * $rule = new ezcConsoleOptionRule( $option, array( 'a', 'b', 'c' ) );
     * </code>
     * If you want to allow only 1 specific value for a parameter, you do not
     * need to wrap this into an array, when creating the rule. Simply use
     * <code>
     * $rule = new ezcConsoleOptionRule( $option, 'a' );
     * </code>
     * to create a rule, that allows the desired parameter only to accept the
     * value 'a'.
     *
     * @param ezcConsoleOption $option The parameter to refer to.
     * @param mixed $values The values $option may have assigned.
     */
    public function __construct( ezcConsoleOption $option, array $values = array() )
    {
        $this->option = $option;
        $this->values = $values;
    }
    
    /**
     * Property read access overloading.
     * Gain read access to properties.
     * 
     * @param string $key Name of the property to access.
     * @return mixed Value of the property.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If the property tried to access does not exist.
     */
    public function __get( $key ) 
    {
        switch ( $key )
        {
            case 'option':
                return $this->option;
                break;
            case 'values':
                return $this->values;
                break;
        }
        throw new ezcBasePropertyNotFoundException( $key );
    }
    
    /**
     * Property read access overloading.
     * Gain read access to properties.
     * 
     * @param string $key Name of the property to access.
     * @return mixed Value of the property.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If the property tried to access does not exist.
     */
    public function __set( $key, $val ) 
    {
        switch ( $key )
        {
            case 'option':
                if ( !( $val instanceof ezcConsoleOption ) )
                {
                    throw new ezcBaseTypeException( 'ezcConsoleOption', gettype( $val ) );
                }
                $this->option = $val;
                return;
                break;
            case 'values':
                if ( !is_array( $val ) )
                {
                    throw new ezcBaseTypeException( 'array', gettype( $val ) );
                }
                $this->values = $val;
                return;
                break;
        }
        throw new ezcBasePropertyNotFoundException( $key );
    }
 
    /**
     * Property isset access.
     * 
     * @param string $key Name of the property.
     * @return bool True is the property is set, otherwise false.
     */
    public function __isset( $key )
    {
        switch ( $key )
        {
            case 'option':
            case 'values':
                return true;
                break;
        }
        return false;
    }

}

?>
