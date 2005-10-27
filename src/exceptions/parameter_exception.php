<?php
/**
 * File containing the ezcConsoleParameterException class.
 *
 * @package ConsoleTools
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 * @filesource
 */

/**
 * General exception for use in {@see ezcConsoleParameter} class.
 * Adds an additional field 'param' to the exception which indicates
 * with which parameter something went wrong.
 * 
 * @package ConsoleTools
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 */
class ezcConsoleParameterException extends Exception
{

    /**
     * Required parameter/alias does not exist.
     */
    const PARAMETER_NOT_EXISTS        = 1;
    /**
     * Exclusion rule defined for parameter not met.
     */
    const PARAMETER_EXCLUSION_RULE_NOT_MET        = 2;
    /**
     * Dependency rule defined for parameter not met.
     */
    const PARAMETER_DEPENDENCY_RULE_NOT_MET       = 3;
    /**
     * Type rule defined for parameter not met.
     */
    const PARAMETER_TYPE_RULE_NOT_MET             = 4;
    /**
     * Unknown string in parameter row.
     */
    const UNKNOWN_PARAMETER          = 5;
    /**
     * No value has been passed to a parameter that expects one.
     */
    const MISSING_PARAMETER_VALUE          = 6;
    /**
     * Multiple values have been passed to a parameter that expects only one.
     */
    const TOO_MANY_PARAMETER_VALUES         = 7;
    /**
     * Arguments were submitted although a parameter disallowed them.
     */
    const ARGUMENTS_NOT_ALLOWED        = 8;
    /**
     * Parameter definition string was not well formed. 
     */
    const PARAMETER_STRING_NOT_WELLFORMED    = 9;

    /**
     * Parameter this exception is about.
     * Shortcut name of the parameter this exception deals with.
     *
     * @see ezcConsoleParameter::registerParam()
     *
     * @var string
     */
    public $paramName;
    
    /**
     * Constructor
     * The constructor additionally needs a parameter name, which is
     * the shortcut name of the affected parameter.
     * For error codes, see class constants!
     *
     * @param string string $message   Error message.
     * @param int $code                Error code.
     * @param string string $paramName Name of affected parameter
     */
    public function __construct( $message, $code, $paramName = null ) {
        $this->paramName = $paramName;
        parent::__construct( $message, $code );
    }
}
