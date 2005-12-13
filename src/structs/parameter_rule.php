<?php
/**
 * File containing the ezcConsoleParameterRule class.
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
 * @see ezcConsoleParameterStruct
 * 
 * @package ConsoleTools
 * @version //autogen//
 */
class ezcConsoleParameterRule {

    /**
     * The parameter this rule refers to. 
     * 
     * @var ezcConsoleParameterStruct
     */
    public $parameter;

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
     * $rule = new ezcConsoleParameterRule( $param, array( 'a', 'b', 'c' ) );
     * </code>
     * If you want to allow only 1 specific value for a parameter, you do not
     * need to wrap this into an array, when creating the rule. Simply use
     * <code>
     * $rule = new ezcConsoleParameterRule( $param, 'a' );
     * </code>
     * to create a rule, that allows the desired parameter only to accept the
     * value 'a'.
     *
     * @param ezcConsoleParameterStruct $param The parameter to refer to.
     * @param mixed $values The values $param may have assigned.
     */
    public function __construct( ezcConsoleParameterStruct $param, $values = array() )
    {
        $this->parameter = $param;
        if ( is_array( $values ) )
        {
            $this->values = $values;
        }
        else
        {
            $this->values = array($values);
        }
    }

    /**
     * Add a value to the rule.
     * Adds a value to the rule. If the value is already registered,
     * the method call will simply be ignored.
     * 
     * @param mixed $value The value to add.
     */
    public function addValue( $value )
    {
        foreach ( $this->values as $id => $val )
        {
            if ( $val === $value )
            {
                return;
            }
        }
        $this->values[] = $value;
    }

    /**
     * Remove a registered value.
     * Removes the given value from the parameter rule, if it is set. If the
     * value is not registered with this rule, the method call will simply be 
     * ignored.
     * 
     * @param mixed $value The value to remove.
     */
    public function removeValue( $value )
    {
        foreach ( $this->values as $id => $val )
        {
            if ( $val === $value )
            {
                unset($this->values[$id]);
            }
        }
    }

    /**
     * Returns if the given value is registered with this rule. 
     * 
     * @param mixed $value The value to check for.
     * @return bool True if the value is registered, otherwise false.
     */
    public function hasValue( $value )
    {
        foreach ( $this->values as $id => $val )
        {
            if ( $val === $value )
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Reset all registered values.
     * This cleans up the possible values the parameter refered to may
     * accept.
     */
    public function resetValues()
    {
        $this->values = array();
    }
    
    /**
     * Overloading.
     * Make the values attribute a read-only property.
     * 
     * @param mixed $key 
     * @return void
     */
    public function __get( $key ) 
    {
        if ( $key === 'values' )
        {
            return $this->values;
        }
    }
}

?>
