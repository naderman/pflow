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
     * @var ReflectionClass
     *      This is the class which this method object has been instantiated
     *      for. It is necessary to decide if a method is definied, inherited,
     *      overridden in a class.
     */
    protected $curClass;

    /**
     * @var ReflectionMethod
     */
    protected $reflectionSource = null;

    /**
     * Constructs an new ezcReflectionMethod
     *
     * @param mixed $classOrSource
     *        Name of class, ReflectionClass, or ReflectionMethod
     * @param string $name
     *        Optional if $classOrSource is an instance of ReflectionMethod
     */
    public function __construct($classOrSource, $name = null) {
    	if ( $classOrSource instanceof ReflectionMethod ) {
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
     * Returns the parameters of the method
     *
     * @return ezcReflectionParameter[] Parameters of the method
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
     *
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
     *
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
     * Returns the short description from the method's documentation
     *
     * @return string Short description
     */
    public function getShortDescription() {
        return $this->docParser->getShortDescription();
    }

    /**
     * Returns the long description from the method's documentation
     *
     * @return string Long description
     */
    public function getLongDescription() {
        return $this->docParser->getLongDescription();
    }

    /**
     * Checks whether the method is annotated with the annotation $annotation
     *
     * @param string $annotation Name of the annotation
     * @return boolean True if the annotation exists for this method
     */
    public function isTagged($annotation) {
        return $this->docParser->isTagged($annotation);
    }

    /**
     * Returns an array of annotations (optinally only annotations of a given name)
     *
     * @param string $name Name of the annotations
     * @return ezcReflectionDocTag[] Annotations
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
     *
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
     *
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
     *
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
     *
     * @return boolean
     */
    function isIntroduced() {
        return !$this->isInherited() and !$this->isOverridden();
    }

    /**
     * Returns the class the method was declared in
     *
     * @return ezcReflectionClassType Class declaring the method
     */
    function getDeclaringClass() {
        if ( $this->reflectionSource ) {
            $class = $this->reflectionSource->getDeclaringClass();
        } else {
            $class = parent::getDeclaringClass();
        }
		if (!empty($class)) {
		    return new ezcReflectionClassType($class->getName());
		}
		else {
		    return null;
		}
    }

    /**
     * Returns the source code of the method
     *
     * @return string Source code
     */
    public function getCode()
    {
        if ( $this->isInternal() ) {
            $code = '/* '
                  . $this->getDeclaringClass()->getName() . '::'
                  . $this->getName()
                  . ' is an internal function.'
                  . ' Therefore the source code is not available. */';
        } else {
            $filename = $this->getFileName();

            $start = $this->getStartLine();
            $end = $this->getEndLine();

            $offset = $start - 1;
            $length = $end - $start + 1;

            $lines = array_slice( file( $filename ), $offset, $length );
            $code = implode( '', $lines );
        }
        return $code;
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
     * Returns the filename of the file this function was declared in
     *
     * @return string Filename of the file this function was declared in
     */
    public function getFileName() {
        if ( $this->reflectionSource instanceof ReflectionMethod ) {
    		return $this->reflectionSource->getFileName();
    	} else {
    		return parent::getFileName();
    	}
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
