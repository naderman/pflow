<?php
/**
 * File containing the ezcReflectionMethod class.
 *
 * @package Reflection
 * @version //autogen//
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Extends the ReflectionMethod class using PHPDoc comments to provide
 * type information
 *
 * @package Reflection
 * @version //autogen//
 * @author Stefan Marr <mail@stefan-marr.de>
 * @author Falko Menge <mail@falko-menge.de>
 */
class ezcReflectionMethod extends ReflectionMethod
{
    /**
     * @var ezcReflectionDocParser
     */
    protected $docParser;

    /**
     * This is the class for which this method object has been instantiated.
     * It is necessary to decide if a method is definied, inherited, overridden
     * in a class.
     *
     * @var ReflectionClass
     */
    protected $curClass;

    /**
     * @var ReflectionMethod
     */
    protected $reflectionSource = null;

    /**
    * @param mixed $classOrSource name of class, ReflectionClass, or ReflectionMethod
    * @param string $name Optional if $classOrSource is instance of ReflectionMethod
    */
    public function __construct($classOrSource, $name = null) {
    	if ($classOrSource instanceof ReflectionMethod ) {
    		$this->reflectionSource = $classOrSource;
    	}
		elseif ($classOrSource instanceof ReflectionClass) {
			parent::__construct($classOrSource->getName(), $name);
            $this->curClass = $classOrSource;
        }
        elseif (is_string($classOrSource)) {
			parent::__construct($classOrSource, $name);
            $this->curClass = new ReflectionClass($classOrSource);
        }
        else {
            $this->curClass = null;
        }

		$this->docParser = ezcReflectionApi::getDocParserInstance();
        $this->docParser->parse($this->getDocComment());
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

    /**
    * @return ezcReflectionParameter[]
    */
    function getParameters() {
        $params = $this->docParser->getParamTags();
        $extParams = array();
        $apiParams = parent::getParameters();
        foreach ($apiParams as $param) {
            $found = false;
            foreach ($params as $tag) {
                if (
                    $tag instanceof ezcReflectionDocTagparam
            	    and $tag->getParamName() == $param->getName()
                ) {
            	   $extParams[] = new ezcReflectionParameter($tag->getType(),
            	                                             $param);
            	   $found = true;
            	   break;
            	}
            }
            if (!$found) {
                $extParams[] = new ezcReflectionParameter(null, $param);
            }
        }
        return $extParams;
    }

    /**
    * Returns the type defined in PHPDoc tags
    * @return ezcReflectionType
    */
    function getReturnType() {
        $re = $this->docParser->getReturnTags();
        if (count($re) == 1 and isset($re[0]) and $re[0] instanceof ezcReflectionDocTagReturn) {
            return ezcReflectionApi::getTypeByName($re[0]->getType());
        }
        return null;
    }

    /**
    * Returns the description after a PHPDoc tag
    * @return string
    */
    function getReturnDescription() {
        $re = $this->docParser->getReturnTags();
        if (count($re) == 1 and isset($re[0])) {
            return $re[0]->getDescription();
        }
        return '';
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
     * Checks if this method is a 'Magic Method' or not
     * @return boolean
     */
    function isMagic() {
        $magicArray =  array('__construct','__destruct','__call',
        					 '__get','__set','__isset','__unset',
        					 '__sleep','__wakeup','__toString','__clone');
        return in_array($this->getName(), $magicArray);
    }

    /**
     * Checks if this is already available in the parent class
     * @return boolean
     */
    function isInherited() {
        $decClass = $this->getDeclaringClass();
        if (!empty($this->curClass) and !empty($decClass)) {
            return ($decClass->getName() != $this->curClass->getName());
        }

        return false;
    }

    /**
     * Checks if this method is redefined in this class
     * @return boolean
     */
    function isOverridden() {
        $decClass = $this->getDeclaringClass();
        if (!empty($this->curClass) and !empty($decClass)) {
            $parent = $this->curClass->getParentClass();
            if (!is_object($parent)) {
                return false;
            }
            else {
                return ($parent->hasMethod($this->getName()) and
                        $this->curClass->getName() == $decClass->getName());
            }
        }
        return false;
    }

    /**
     * Checks if this method is appeared first in the current class
     * @return boolean
     */
    function isIntroduced() {
        return !$this->isInherited() and !$this->isOverridden();
    }

    /**
     * @return ezcReflectionClassType
     */
    function getDeclaringClass() {
        $class = parent::getDeclaringClass();
		if (!empty($class)) {
		    return new ezcReflectionClassType($class->getName());
		}
		else {
		    return null;
		}
    }


    // the following methods do not contain additional features
    // they just call the parent method or the reflection source

    /**
     * Returns the doc comment for the method.
     *
     * @return string Doc comment
     */
    public function getDocComment() {
        if ( $this->reflectionSource instanceof ReflectionMethod ) {
            $comment = $this->reflectionSource->getDocComment();
        } else {
            $comment = parent::getDocComment();
        }
        return $comment;
    }

    /**
     * Name of the method
     * @return string
     */
    public function getName() {
        if ( $this->reflectionSource instanceof ReflectionMethod ) {
    		return $this->reflectionSource->getName();
    	} else {
    		return parent::getName();
    	}
    }
}
?>
