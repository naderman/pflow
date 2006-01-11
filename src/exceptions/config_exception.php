<?php
/**
 * File containing the ezcBaseConfigException class.
 *
 * @package Base
 * @version //autogen//
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * ezcBaseConfigException is thrown whenever there is something wrong with
 * configuration that is set through the setOptions() method of a class.
 *
 * @package Base
 */
class ezcBaseConfigException extends ezcBaseException
{
    /**
     * Used for when a config setting was unknown
     */
    const UNKNOWN_CONFIG_SETTING = 1;

    /**
     * Used when an option's value was out of range
     */
    const VALUE_OUT_OF_RANGE = 2;

    /**
     * Constructs a new ezcBaseConfigException
     *
     * Constructs a new ezcBaseConfigException
     *
     * @param string  $settingName The name of the setting where something was
     *                wrong with.
     * @param int $exceptionType The type of exception (use one of the
     *                class' constants for this)
     * @param mixed   $value The value that the option was tried to be set too
     */
    function __construct( $settingName, $exceptionType, $value = null )
    {
        switch ( $exceptionType )
        {
            case self::UNKNOWN_CONFIG_SETTING:
                $msg = "The setting <{$settingName}> is not a valid configuration setting.";
                break;
            case self::VALUE_OUT_OF_RANGE:
                $msg = "The value <{$value}> that you were trying to assign to setting <{$settingName}> is invalid.";
                break;
        }
        parent::__construct( $msg, $exceptionType );
    }
}
?>
