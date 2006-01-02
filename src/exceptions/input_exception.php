<?php
/**
 * File containing the ezcConsoleInputException class.
 *
 * @package ConsoleTools
 * @version //autogentag//
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * General exception for use in {@link ezcConsoleInput} class.
 * Adds an additional field 'param' to the exception which indicates
 * with which parameter something went wrong.
 * 
 * @package ConsoleTools
 * @version //autogen//
 */
class ezcConsoleInputException extends Exception
{
    /**
     * Required parameter/alias does not exist.
     */
    const PARAMETER_NOT_EXISTS = 1;
    /**
     * Exclusion rule defined for parameter not met.
     */
    const PARAMETER_EXCLUSION_RULE_NOT_MET = 2;
    /**
     * Dependency rule defined for parameter not met.
     */
    const PARAMETER_DEPENDENCY_RULE_NOT_MET = 3;
    /**
     * Type rule defined for parameter not met.
     */
    const PARAMETER_TYPE_RULE_NOT_MET = 4;
    /**
     * Unknown string in parameter row.
     */
    const UNKNOWN_PARAMETER = 5;
    /**
     * No value has been passed to a parameter that expects one.
     */
    const MISSING_PARAMETER_VALUE = 6;
    /**
     * Multiple values have been passed to a parameter that expects only one.
     */
    const TOO_MANY_PARAMETER_VALUES = 7;
    /**
     * Arguments were submitted although a parameter disallowed them.
     */
    const ARGUMENTS_NOT_ALLOWED = 8;
    /**
     * Parameter definition string was not well formed. 
     */
    const PARAMETER_STRING_NOT_WELLFORMED = 9;
    /**
     * A parameter with the same short/long name is already registered.
     */
    const PARAMETER_ALREADY_REGISTERED = 10;
    /**
     * The parameter refered to is a real parameter, not an alias. 
     */
    const PARAMETER_IS_NO_ALIAS = 11;
    /**
     * The given parameter name is invalid. Maybe you prefixed the name with '-' / '--'?
     */
    const PARAMETER_NAME_INVALID = 12;

    /**
     * Parameter this exception is about.
     * Stores the parameter this exception is about. This attribute is optional.
     *
     * @see ezcConsoleInput::registerOption()
     *
     * @var ezcConsoleOption
     */
    public $option;
    
    /**
     * Constructor
     * The constructor additionally needs a parameter name, which is
     * the shortcut name of the affected parameter.
     * For error codes, see class constants!
     *
     * @param string string $message   Error message.
     * @param int $code                Error code.
     * @param string string $optionName Name of affected parameter
     */
    public function __construct( $message, $code, ezcConsoleOption $option = null )
    {
        $this->option = isset( $option ) ? $option : null;
        parent::__construct( $message, $code );
    }
}
?>
