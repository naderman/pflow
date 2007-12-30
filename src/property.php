<?php
/**
 * File containing the ezcReflectionProperty class.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Extends the ReflectionProperty class using PHPDoc comments to provide
 * type information
 * 
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 */
class ezcReflectionProperty extends ReflectionProperty {
    
	/**
    * @var ezcReflectionDocParser
    */
    protected $docParser = null;
	
	/**
	* @var ReflectionProperty
	*/
	protected $reflectionSource = null;

    /**
    * @param mixed $class
    * @param string $name
    */
    public function __construct($class, $name) {
		if (!$class instanceof ReflectionProperty) {
			parent::__construct($class, $name);
		}
		$this->reflectionSource = $class;

        $this->docParser = ezcReflectionApi::getDocParserInstance();
		$this->docParser->parse($this->getDocComment());
    }

    /**
    * @return ezcReflectionType
    */
    public function getType() {
        if ($this->docParser == null) {
            return 'unknown(ReflectionProperty::getDocComment introduced at'.
                   ' first in PHP5.1)';
        }

        $vars = $this->docParser->getVarTags();
        if (isset($vars[0])) {
            return ezcReflectionApi::getTypeByName($vars[0]->getType());
        }
        else {
            return null;
        }
    }

    /**
    * @return ezcReflectionClassType
    */
    public function getDeclaringClass() {
		if ( $this->reflectionSource instanceof ReflectionProperty ) {
			return new ezcReflectionClassType($this->reflectionSource->getDeclaringClass());
		} else {
			$class = parent::getDeclaringClass();
			return new ezcReflectionClassType($class->getName());
		}
    }
	
	/**
     * Returns the doc comment for the class.
     *
     * @return string doc comment
     */
    public function getDocComment() {
        if ( $this->reflectionSource instanceof ReflectionProperty )
        {
            // query external reflection object
            $comment = $this->reflectionSource->getDocComment();
        } else {
            $comment = parent::getDocComment();
        }
        return $comment;
    }
	
	/**
     * Returns the name of the property.
     * @return string property name
     */
    public function getName() {
        if ( $this->reflectionSource instanceof ReflectionProperty ) {
            $name = $this->reflectionSource->getName();
        } else {
            $name = parent::getName();
        }
        return $name;
    }
	
	/**
     * Returns true if this property has public as access level.
     * @return bool
     */
    public function isPublic() {
        if ( $this->reflectionSource instanceof ReflectionProperty ) {
            return $this->reflectionSource->isPublic();
        } else {
            return parent::isPublic();
        }
    }
	
	/**
     * Returns true if this property has protected as access level.
     * @return bool
     */
    public function isProtected() {
        if ( $this->reflectionSource instanceof ReflectionProperty ) {
            return $this->reflectionSource->isProtected();
        } else {
            return parent::isProtected();
        }
    }
	
	/**
     * Returns true if this property has private as access level.
     * @return bool
     */
    public function isPrivate() {
        if ( $this->reflectionSource instanceof ReflectionProperty ) {
            return $this->reflectionSource->isPrivate();
        } else {
            return parent::isPrivate();
        }
    }
	
	/**
     * Returns true if this property has is a static property.
     * @return bool
     */
    public function isStatic() {
        if ( $this->reflectionSource instanceof ReflectionProperty ) {
            return $this->reflectionSource->isStatic();
        } else {
            return parent::isStatic();
        }
    }
	
	/**
	 * A default property is defined in the class definition.
	 * A non-default property is an instance specific state.
     * @return bool
     */
    public function isDefault() {
        if ( $this->reflectionSource instanceof ReflectionProperty ) {
            return $this->reflectionSource->isDefault();
        } else {
            return parent::isDefault();
        }
    }
	
	/**
     * @return int
     */
    public function getModifiers() {
        if ( $this->reflectionSource instanceof ReflectionProperty ) {
            return $this->reflectionSource->getModifiers();
        } else {
            return parent::getModifiers();
        }
    }
	
	/**
     * @return mixed
     */
    public function getValue($object = null) {
        if ( $this->reflectionSource instanceof ReflectionProperty ) {
            return $this->reflectionSource->getValue($object);
        } else {
            return parent::getValue($object);
        }
    }
	
	/**
	 * @param mixed $value
     */
    public function setValue($object = null, $value) {
        if ( $this->reflectionSource instanceof ReflectionProperty ) {
            $this->reflectionSource->setValue($object, $value);
        } else {
            parent::setValue($object, $value);
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
