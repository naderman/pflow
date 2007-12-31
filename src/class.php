<?php
/**
 * File containing the ezcReflectionClass class.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Extends the ReflectionClass using PHPDoc comments to provide
 * type information
 * 
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 * @author Falko Menge <mail@falko-menge.de>
 */
class ezcReflectionClass extends ReflectionClass
{
    /**
     * @var ezcReflectionDocParser
     */
    protected $docParser;

    /**
     * @var string|object|ReflectionClass
     *      name, instance or ReflectionClass object of the class to be
     *      reflected
     */
    protected $class;

    /**
     * @param string|object|ReflectionClass $argument
     *        name, instance or ReflectionClass object of the class to be
     *        reflected
     */
    public function __construct( $argument )
    {
        if ( !$argument instanceof ReflectionClass )
        {
            parent::__construct($argument);
        }
        $this->class = $argument;
        $this->docParser = ezcReflectionApi::getDocParserInstance();
        $this->docParser->parse($this->getDocComment());
    }
    
    /**
     * Use overloading to call additional methods
     * of the reflection instance given to the constructor
     *
     * @param string $method Method to be called
     * @param array<integer, mixed> $arguments Arguments that were passed
     * @return mixed
     */
    public function __call( $method, $arguments )
    {
        if ( $this->class instanceof ReflectionClass )
        {
            // query external reflection object
            return call_user_func_array( array($this->class, $method), $arguments );
        } else {
            throw new Exception( 'Call to undefined method ' . __CLASS__ . '::' . $method );
        }
    }

    /**
     * Returns the name of the class.
     *
     * @return string Classname
     */
    public function getName() {
        if ( $this->class instanceof ReflectionClass )
        {
            // query external reflection object
            $name = $this->class->getName();
        } else {
            $name = parent::getName();
        }
        return $name;
    }

    /**
     * Returns the doc comment for the class.
     *
     * @return string Doc comment
     */
    public function getDocComment() {
        if ( $this->class instanceof ReflectionClass )
        {
            // query external reflection object
            $comment = $this->class->getDocComment();
        } else {
            $comment = parent::getDocComment();
        }
        return $comment;
    }

    /**
     * Returns an ezcReflectionMethod object of the method specified by $name.
     *
     * @param string $name Name of the method
     * @return ezcReflectionMethod
     */
    public function getMethod($name) {
    	if ( $this->class instanceof ReflectionClass ) {
    		return new ezcReflectionMethod($this->class->getMethod($name));
    	} else {
    		return new ezcReflectionMethod(parent::getMethod($name));
    	}
    }

    /**
     * Returns an ezcReflectionMethod object of the constructor method.
     *
     * @return ezcReflectionMethod
     */
    public function getConstructor() {
        if ($this->class instanceof ReflectionClass) {
            // query external reflection object
            $constructor = $this->class->getConstructor();
        } else {
            $constructor = parent::getConstructor();
        }
        
        if ($constructor != null) {
            return new ezcReflectionMethod($constructor);
        } else {
            return null;
        }
    }

    /**
     * Returns the methods as an array of ezcReflectionMethod objects.
     *
     * @param integer $filter
     *        A combination of
     *        ReflectionMethod::IS_STATIC,
     *        ReflectionMethod::IS_PUBLIC,
     *        ReflectionMethod::IS_PROTECTED,
     *        ReflectionMethod::IS_PRIVATE,
     *        ReflectionMethod::IS_ABSTRACT and
     *        ReflectionMethod::IS_FINAL
     * @return ezcReflectionMethod[]
     */
    public function getMethods($filter = null) {
        $extMethods = array();
        if ( $this->class instanceof ReflectionClass ) {
            $methods = $this->class->getMethods($filter);
        } else {
            $methods = parent::getMethods($filter);
        }
        foreach ($methods as $method) {
            $extMethods[] = new ezcReflectionMethod($method);
        }
        return $extMethods;
    }
    
    /**
     * Returns an array of all interfaces implemented by the class.
     * @return ezcReflectionClass[]
     */
    public function getInterfaces() {
    	if ( $this->class instanceof ReflectionClass ) {
    		$ifaces = $this->class->getInterfaces();
    	} else {
    		$ifaces = parent::getInterfaces();
    	}
    	
    	$result = array();
    	foreach ($ifaces as $i) {
    		$result[] = new ezcReflectionClassType($i);
    	}
    	return $result;
    }

    /**
     * @return ezcReflectionClassType
     */
    public function getParentClass()
    {
        if ( $this->class instanceof ReflectionClass )
        {
            // query external reflection object
            $parentClass = $this->class->getParentClass();
        } else {
            $parentClass = parent::getParentClass();
        }
        
        if (is_object($parentClass)) {
            return new ezcReflectionClassType($parentClass);
        }
        else {
            return null;
        }
    }

    /**
     * @param string $name
     * @return ezcReflectionProperty
     * @throws RelectionException if property doesn't exists
     */
    public function getProperty($name) {
		if ( $this->class instanceof ReflectionClass )
        {
            // query external reflection object
            $prop = $this->class->getProperty($name);
        } else {
            $prop = parent::getProperty($name);
        }
        
		if (is_object($prop) && !($prop instanceof ezcReflectionProperty)) {
			return new ezcReflectionProperty($prop, $name);
        } else {
			// TODO: may be we should throw an exception here
            return $prop;
        }
    }

    /**
     * @param integer $filter a combination of ReflectionProperty::IS_STATIC,
     * ReflectionProperty::IS_PUBLIC, ReflectionProperty::IS_PROTECTED,
     * ReflectionProperty::IS_PRIVATE
     * @return ezcReflectionProperty[]
     */
    public function getProperties($filter = null) {
        if ( $this->class instanceof ReflectionClass ) {
        	$props = $this->class->getProperties($filter);
        } else {
        	$props = parent::getProperties($filter);
        }
        
        $extProps = array();
        foreach ($props as $prop) {
            $extProps[] = new ezcReflectionProperty( $prop );
        }
        return $extProps;
    }

    /**
     * Check whether this class has been tagged with @webservice
     * @return boolean
     */
    public function isWebService() {
        return $this->docParser->isTagged("webservice");
    }

    /**
     * @return string
     */
    public function getShortDescription() {
        return $this->docParser->getShortDescription();
    }

    /**
     * @return string
     */
    public function getLongDescription() {
        return $this->docParser->getLongDescription();
    }

    /**
     * @param string $with
     * @return boolean
     */
    public function isTagged($with) {
        return $this->docParser->isTagged($with);
    }

    /**
     * @param string $name
     * @return ezcReflectionDocTag[]
     */
    public function getTags($name = '') {
        if ($name == '') {
            return $this->docParser->getTags();
        }
        else {
            return $this->docParser->getTagsByName($name);
        }
    }

    /**
     * @return ezcReflectionExtension
     */
    public function getExtension() {
    	if ( $this->class instanceof ReflectionClass ) {
    		$ext = $this->class->getExtension();
    	} else {
    		$ext = parent::getExtension();
    	}
    	
        if ($ext) {
            return new ezcReflectionExtension($ext);
        } else {
            return null;
        }
    }
}
?>
