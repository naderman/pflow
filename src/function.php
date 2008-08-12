<?php
/**
 * File containing the ezcReflectionFunction class.
 *
 * @package Reflection
 * @version //autogentag//
 * @copyright Copyright (C) 2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Extends the ReflectionFunction class using PHPDoc comments to provide
 * type information
 *
 * @package Reflection
 * @version //autogentag//
 * @author Stefan Marr <mail@stefan-marr.de>
 */
class ezcReflectionFunction extends ReflectionFunction
{
    /**
     * @var ezcReflectionDocParser Parser for source code annotations
     */
    protected $docParser;

    /**
     * @var string|ReflectionFunction
     *     ReflectionFunction object or function name used to initialize this
     *     object
     */
    protected $reflectionSource;

    /**
     * Constructs a new ezcReflectionFunction object
     *
     * Throws an Exception in case the given function does not exist
     * @param string|ReflectionFunction $name
     *        Name or ReflectionFunction object of the function to be reflected
     */
    public function __construct( $name ) {
        if ( !$name instanceof ReflectionFunction ) {
            parent::__construct( $name );
        }
        $this->reflectionSource = $name;

        $this->docParser = ezcReflectionApi::getDocParserInstance();
        $this->docParser->parse( $this->getDocComment() );
    }

    /**
     * Returns a string representation
     * @return string
     */
    public function __toString() {
        if ( $this->reflectionSource ) {
            return $this->reflectionSource->__toString();
        } else {
            return parent::__toString();
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
                    $extParams[] = new ezcReflectionParameter(
                        $tag->getType(),
                        $param
                    );
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
     * Check whether this method has a @webmethod tag
     * @return boolean
     */
    function isWebmethod() {
        return $this->docParser->isTagged("webmethod");
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
     * @return boolean
     */
    public function isDisabled() {
        if ($this->reflectionSource instanceof ReflectionFunction ) {
            return $this->reflectionSource->isDisabled();
        } else {
            return parent::isDisabled();
        }
    }
}
?>
