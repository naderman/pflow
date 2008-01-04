<?php
/**
 * File containing the ezcReflectionExtension class.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Extends the ReflectionExtension class using PHPDoc comments to provide
 * type information
 * 
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 */
class ezcReflectionExtension extends ReflectionExtension {

	/**
	 * @var ReflectionExtension
	 */
	protected $reflectionSource = null;
	
    /**
    * @param string|ReflectionExtension $extension
    */
    public function __construct($extension) {
    	if ( $extension instanceof ReflectionExtension ) {
    		$this->reflectionSource = $extension;
    	} else {
        	parent::__construct( $extension );
    	}
    }

    /**
    * @return ezcReflectionFunction[]
    */
    public function getFunctions() {
    	if ( $this->reflectionSource ) {
    		$functs = $this->reflectionSource->getFunctions();
    	} else {
        	$functs = parent::getFunctions();
    	}
    	
        $result = array();
        foreach ($functs as $func) {
        	$result[] = new ezcReflectionFunction($func);
        }
        return $result;
    }

    /**
     * @return ezcReflectionClassType[]
     */
    public function getClasses() {
    	if ( $this->reflectionSource ) {
    		$classes = $this->reflectionSource->getClasses();
    	} else {
        	$classes = parent::getClasses();
    	}
    	
        $result = array();
        foreach ($classes as $class) {
        	$result[] = new ezcReflectionClassType($class);
        }
        return $result;
    }
    
    public function getName() {
    	if ( $this->reflectionSource ) {
    		return $this->reflectionSource->getName();
    	} else {
    		return parent::getName();
    	}
    }
    
    public function getVersion() {
    	if ( $this->reflectionSource ) {
    		return $this->reflectionSource->getVersion();
    	} else {
    		return parent::getVersion();
    	}
    }
    
    public function getConstants() {
    	if ( $this->reflectionSource ) {
    		return $this->reflectionSource->getConstants();
    	} else {
    		return parent::getConstants();
    	}
    }
    
    public function getINIEntries() {
    	if ( $this->reflectionSource ) {
    		return $this->reflectionSource->getINIEntries();
    	} else {
    		return parent::getINIEntries();
    	}
    }
    
    public function getClassNames() {
    	if ( $this->reflectionSource ) {
    		return $this->reflectionSource->getClassNames();
    	} else {
    		return parent::getClassNames();
    	}
    }
    
    public function info() {
    	if ( $this->reflectionSource ) {
    		return $this->reflectionSource->info();
    	} else {
    		return parent::info();
    	}
    }
    
    /**
     * Use overloading to call additional methods
     * of the reflection instance given to the constructor
     *
     * @param string $method Method to be called
     * @param array(integer => mixed) $arguments Arguments that were passed
     * @return mixed
     */
    public function __call( $method, $arguments )
    {
        if ( $this->reflectionSource ) {
            return call_user_func_array( array($this->reflectionSource, $method), $arguments );
        } else {
            throw new Exception( 'Call to undefined method ' . __CLASS__ . '::' . $method );
        }
    }
}
?>