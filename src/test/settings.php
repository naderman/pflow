<?php
/**
 * File containing the ezcTestSettings class
 *
 * @package UnitTest
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license BSD {@link http://ez.no/licenses/bsd}
 */

/**
 * This object stores the settings from the TestRunner. Every TestCase can get
 * the instance of this object, and access the settings. 
 * 
 * @package UnitTest
 * @author
 * @version //autogen//
 */
class ezcTestSettings
{
   /**
     * Holds the properties
     */
    private $properties = array();

    /**
     * Holds the one and only instance of this object.
     */
    private static $instance = null;

    /**
     * Use the getInstance() method instead to get an instance of this class.
     */
    private function __construct()
    {
        $this->properties['db'] = new ezcTestDatabaseSettings;
    }

    /**
     * Returns an instance of this class.
     */
	public static function getInstance()
	{
        if ( is_null( ezcTestSettings::$instance ))
        {
            ezcTestSettings::$instance = new ezcTestSettings();
        }

        return ezcTestSettings::$instance;
	}

    /** 
     * No properties can be set.
     */
   public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'db':
               trigger_error( "Property: db is read-only", E_USER_ERROR );
               break;
        }
    }

    /**
     * The db property can be read.
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'db':
                return $this->properties['db'];
                break;
            default:
                return parent::__get( $name );
                break;
        }
    }

    /**
     * Set all the database settings via a given settings array.
     */
    public function setDatabaseSettings( $settings )
    {
        $settingNames = array(
            'dsn', 'phptype', 'dbsyntax', 'username', 'password', 'protocol',
            'hostspec', 'port', 'socket', 'database'
        );
        foreach ( $settingNames as $settingName )
        {
            if ( isset( $settings[$settingName] ) )
            {
                $this->properties['db']->$settingName = $settings[$settingName];
            }
        }
    }
}
?>
