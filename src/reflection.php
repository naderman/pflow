<?php
/**
 * File containing the ezcReflectionApi class.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

// TODO: rename ezcReflectionApi to ezcReflection
/**
 * Holds type factory for generating type objects by given name
 * 
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 * @author Falko Menge <mail@falko-menge.de>
 */
class ezcReflectionApi {

	/**
	 * @var ezcReflectionTypeFactory
	 */
	protected static $reflectionTypeFactory = null;
	
	/**
	 * @var ezcReflectionDocParser
     *      Current documentation parser used by all ezcReflection classes
	 */
	protected static $docParser = null;

	/**
	 * Don't allow objects, it is just a static factory
	 */
    // @codeCoverageIgnoreStart
    private function __construct() {}
    // @codeCoverageIgnoreEnd

    // TODO: rename getDocParserInstance() to getDocParser()
    /**
     * Returns a copy of the current documentation parser used by all
     * ezcReflection classes
     *
     * @return ezcReflectionDocParser
     */
    public static function getDocParserInstance()
    {
    	if (self::$docParser == null) {
    		self::$docParser = new ezcReflectionPhpDocParser();
    	}
    	return clone self::$docParser;
    }
    
    /**
     * Sets the documentation parser used by all ezcReflection classes
     *
     * @param setDocParser $docParser Parser for documentation blocks
     * @return void
     */
    public static function setDocParser(ezcReflectionDocParser $docParser)
    {
    	self::$docParser = $docParser;
    }
    
    /**
     * Returns a copy of the current factory used to create type objects
     *
     * @return ezcReflectionTypeFactory
     */
    public static function getReflectionTypeFactory()
    {
        if (self::$reflectionTypeFactory == null) {
            self::$reflectionTypeFactory = new ezcReflectionTypeFactoryImpl();
        }
    	return clone self::$reflectionTypeFactory;
    }
    
    /**
     * Sets the factory used to create type objects
     *
     * @param ezcReflectionTypeFactory $factory Factory for type objects
     * @return void
     */
    public static function setReflectionTypeFactory(ezcReflectionTypeFactory $factory)
    {
        self::$reflectionTypeFactory = $factory;
    }

    /**
     * Returns a ezcReflectionType object for the given type name
     *
     * @param string $typeName
     * @return ezcReflectionType
     */
    public static function getTypeByName($typeName)
    {
        return self::getReflectionTypeFactory()->getType($typeName);
    }

    /**
     * Returns an array with the ezcReflectionClass objects for all declared
     * classes
     *
     * @return ezcReflectionClass[] all declared classes
     */
    public static function getClasses() {
        $classes = array();
        foreach( get_declared_classes() as $className ) {
            $classes[$className] = new ezcReflectionClass( $className );
        }
        return $classes;
    }

    /**
     * Returns an array with the ezcReflectionClass objects for all declared
     * interfaces
     *
     * @return ezcReflectionClass[] all declared interfaces
     */
    public static function getInterfaces() {
        $interfaces = array();
        foreach( get_declared_interfaces() as $interfaceName ) {
            $interfaces[$interfaceName] = new ezcReflectionClass( $interfaceName );
        }
        return $interfaces;
    }

    /**
     * Returns an array with the ezcReflectionFunction objects for all
     * user-defined functions
     *
     * @return ezcReflectionFunction[] all user-defined functions
     */
    public static function getUserDefinedFunctions() {
        $functions = array();
        $functionNames = get_defined_functions();
        foreach( $functionNames['user'] as $functionName ) {
            $functions[$functionName] = new ezcReflectionFunction( $functionName );
        }
        return $functions;
    }

    /**
     * Returns an array with the ezcReflectionFunction objects for all
     * available internal functions
     *
     * @return ezcReflectionFunction[] all internal functions
     */
    public static function getInternalFunctions() {
        $functions = array();
        $functionNames = get_defined_functions();
        foreach( $functionNames['internal'] as $functionName ) {
            $functions[$functionName] = new ezcReflectionFunction( $functionName );
        }
        return $functions;
    }

    /**
     * Returns an array with the ezcReflectionFunction objects for all
     * internal and user-defined functions
     *
     * @return ezcReflectionFunction[] all internal and user-defined functions
     */
    public static function getFunctions() {
        $functions = array();
        $functionNames = get_defined_functions();
        foreach( $functionNames['internal'] as $functionName ) {
            $functions[$functionName] = new ezcReflectionFunction( $functionName );
        }
        foreach( $functionNames['user'] as $functionName ) {
            $functions[$functionName] = new ezcReflectionFunction( $functionName );
        }
        return $functions;
    }
}
?>
