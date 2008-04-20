<?php
/**
 * File containing the ezcReflectionParameter class.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Extends the ReflectionParameter class using PHPDoc comments to provide
 * type information
 * 
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 */
 class ezcReflectionParameter extends ReflectionParameter {

    /**
     * @var ezcReflectionType
     */
    protected $type;

    /**
     * @var ReflectionParameter
     */
    protected $parameter = null;

    /**
     * @param mixed $mixed Type info or $function for parent class
     * @param mixed $parameter ReflectionParameter or $parameter for parent class
     * @param string $type Type information from param tag
     */
    public function __construct($mixed, $parameter) {
        if ($parameter instanceof ReflectionParameter) {
            $this->parameter = $parameter;
            $this->type = ezcReflectionApi::getTypeByName($mixed);
        }
        else {
            parent::__construct($mixed, $parameter);
        }

    }

    /**
     * Returns the type of this parameter in form of an ezcReflectionType
     * @return ezcReflectionType
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Returns whether NULL is allowed as this parameters's value
     * @return boolean
     */
    public function allowsNull() {
        if ($this->parameter != null) {
            return $this->parameter->allowsNull();
        }
        else {
            return parent::allowsNull();
        }
    }

    /**
     * Returns whether this parameter is an optional parameter
     * @return boolean
     */
    public function isOptional() {
        if ($this->parameter != null) {
            return $this->parameter->isOptional();
        }
        else {
            return parent::isOptional();
        }
    }

    /**
     * Returns whether this parameters is passed to by reference
     * @return boolean
     */
    public function isPassedByReference() {
        if ($this->parameter != null) {
            return $this->parameter->isPassedByReference();
        }
        else {
            return parent::isPassedByReference();
        }
    }
	
	/**
     * Returns whether parameter MUST be an array
     * @return boolean
     */
    public function isArray() {
        if ($this->parameter != null) {
            return $this->parameter->isArray();
        }
        else {
            return parent::isArray();
        }
    }

    /**
     * Returns whether the default value of this parameter is available
     * @return boolean
     */
    public function isDefaultValueAvailable() {
        if ($this->parameter != null) {
            return $this->parameter->isDefaultValueAvailable();
        }
        else {
            return parent::isDefaultValueAvailable();
        }
    }

    /**
     * Returns this parameters's name
     * @return string
     */
    public function getName() {
        if ($this->parameter != null) {
            return $this->parameter->getName();
        }
        else {
            return parent::getName();
        }
    }
	
	/**
     * Returns whether this parameter is an optional parameter
     * @return integer
     */
    public function getPosition() {
        if ($this->parameter != null) {
            return $this->parameter->getPosition();
        }
        else {
            return parent::getPosition();
        }
    }

    /**
     * Returns the default value of this parameter or throws an exception
     * @return mixed
     */
    public function getDefaultValue() {
        if ($this->parameter != null) {
            return $this->parameter->getDefaultValue();
        }
        else {
            return parent::getDefaultValue();
        }
    }

    /**
    * Returns reflection object identified by type hinting or NULL if there is no hint
    * @return ezcReflectionClassType
    */
    public function getClass() {
        if ($this->type && $this->type->isClass()) {
            return $this->type;
        }
        return null;
    }

    /**
     * Returns the ezcReflectionFunction for the function of this parameter
     * @return ezcReflectionFunction
     */
    public function getDeclaringFunction() {
        if ($this->parameter != null) {
            $func = $this->parameter->getDeclaringFunction();
        }
        else {
            $func = parent::getDeclaringFunction();
        }
        if (!empty($func)) {
            return new ezcReflectionFunction($func->getName());
        }
        else {
            return null;
        }
	}

    /**
     * Returns in which class this parameter is defined (not the type hint of the parameter)
     * @return ezcReflectionClassType
     */
    function getDeclaringClass() {
        if ($this->parameter != null) {
            $class = $this->parameter->getDeclaringClass();
        }
        else {
            $class = parent::getDeclaringClass();
        }

		if (!empty($class)) {
            //TODO: this changes the semantic of the return value (PHP's Reflection API returns a ReflectionClass)
		    return new ezcReflectionClassType($class->getName());
		}
		else {
		    return null;
		}
    }
}
?>
