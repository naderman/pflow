<?php
/**
 * File containing the ezcBaseConfigException class.
 *
 * @package Base
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 */
/**
 * ezcBaseConfigException is thrown whenever there is something wrong with
 * configuration that is set through the setConfiguration() method of a class.
 *
 * @package Base
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 * @version //autogen//
 */
class ezcBaseConfigException extends Exception
{
    /**
     *
     */
    const UNKNOWN_CONFIG_SETTING = 1;

    /**
     *
     */
    const VALUE_OUT_OF_RANGE = 2;

    /**
     * Constructs a new ezcBaseConfigException
     */
    function __construct( $settingName, $exceptionType, $value = null )
    {
        switch ( $exceptionType )
        {
            case self::UNKNOWN_CONFIG_SETTING:
                $msg = "The setting '{$settingName}' is not a valid configuration setting.";
                break;
            case self::VALUE_OUT_OF_RANGE:
                $msg = "The value '{$value}' that you were trying to assign to setting '{$settingName}' is invalid.";
                break;
        }
        parent::__construct( $msg, $exceptionType );
    }
}
?>
