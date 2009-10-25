<?php
/**
 * I provide completely working code with this framework, which will not be
 * developed any further, because there are already existing packages, which try
 * to provide similar functionallities.
 */

namespace org\pdepend\reflection;

/**
 * Static method implementation.
 *
 * @author  Manuel Pichler <mapi@pdepend.org>
 * @license Copyright by Manuel Pichler
 * @version $Revision$
 */
class StaticReflectionMethod extends \ReflectionMethod
{
    const TYPE = __CLASS__;

    /**
     * @var string
     */
    private $_name = null;

    /**
     * @var string
     */
    private $_docComment = false;

    /**
     * @var integer
     */
    private $_modifiers = 0;

    /**
     * The declaring class.
     *
     * @var \ReflectionClass
     */
    private $_declaringClass = null;

    /**
     * Parameters declared for the reflected method.
     *
     * @var array(\ReflectionParameter)
     */
    private $_parameters = null;

    /**
     * The start line number of the reflected method.
     *
     * @var integer
     */
    private $_startLine = -1;

    /**
     * The end line number of the reflected method.
     *
     * @var integer
     */
    private $_endLine = -1;

    /**
     * @param string  $name
     * @param string  $docComment
     * @param integer $modifiers
     */
    public function __construct( $name, $docComment, $modifiers )
    {
        $this->_setName( $name );
        $this->_setModifiers( $modifiers );
        $this->_setDocComment( $docComment );
    }

    /**
     * Returns the name of the reflected method.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Sets the name of the reflected method.
     *
     * @param string $name Name of the reflected method.
     *
     * @return void
     */
    private function _setName( $name )
    {
        $this->_name = $name;
    }

    /**
     * Returns the method's short name.
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->_name;
    }

    /**
     * Get the namespace name where the class is defined.
     *
     * @return string
     */
    public function getNamespaceName()
    {
        return '';
    }

    /**
     * Returns <b>true</b> when the reflected function is declared in a namespace,
     * otherwise this method will return <b>false</b>.
     *
     * @return boolean
     */
    public function inNamespace()
    {
        return false;
    }

    /**
     * Returns the doc comment of the reflected method or <b>false</b> when no
     * comment was found.
     *
     * @return string|boolean
     */
    public function getDocComment()
    {
        return $this->_docComment;
    }

    /**
     * Sets the doc comment of the reflected method.
     *
     * @param string $docComment Doc comment for the reflected method.
     *
     * @return void
     */
    private function _setDocComment( $docComment )
    {
        if ( trim( $docComment ) !== '' )
        {
            $this->_docComment = $docComment;
        }
    }

    /**
     * Returns the modifiers of the reflected method.
     *
     * @return integer
     */
    public function getModifiers()
    {
        return $this->_modifiers;
    }

    /**
     * Sets and validates the modifiers of the reflected method.
     *
     * @param integer $modifiers The modifiers for the reflected method.
     *
     * @return void
     * @access private
     */
    public function _setModifiers( $modifiers )
    {
        $expected = self::IS_PRIVATE | self::IS_PROTECTED | self::IS_PUBLIC
                  | self::IS_STATIC  | self::IS_ABSTRACT  | self::IS_FINAL;

        if ( ( $modifiers & ~$expected ) !== 0 )
        {
            throw new \ReflectionException( 'Invalid method modifier given.' );
        }
        $this->_modifiers = $modifiers;
    }

    /**
     * Returns the pathname where the reflected method was declared.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->getDeclaringClass()->getFileName();
    }

    /**
     * Returns <b>true</b> when the reflected method is the ctor of the parent
     * class instance.
     *
     * @return boolean
     */
    public function isConstructor()
    {
        if ( $this->isAbstract() )
        {
            return false;
        }
        if ( strcasecmp( $this->getName(), '__construct' ) === 0 )
        {
            return true;
        }
        else if ( $this->getDeclaringClass()->hasMethod( '__construct' ) )
        {
            return false;
        }
        return ( strcasecmp( $this->getName(), $this->getDeclaringClass()->getShortName() ) === 0 );
    }

    /**
     * Returns <b>true</b> when the reflected method is the dtor of the parent
     * class instance.
     *
     * @return boolean
     */
    public function isDestructor()
    {
        return ( strcasecmp( $this->getName(), '__destruct' ) === 0 );
    }

    /**
     * @return boolean
     */
    public function isAbstract()
    {
        return ( ( $this->_modifiers & self::IS_ABSTRACT ) === self::IS_ABSTRACT );
    }

    /**
     * @return boolean
     */
    public function isStatic()
    {
        return ( ( $this->_modifiers & self::IS_STATIC ) === self::IS_STATIC );
    }

    /**
     * @return boolean
     */
    public function isFinal()
    {
        return ( ( $this->_modifiers & self::IS_FINAL ) === self::IS_FINAL );
    }

    /**
     * @return boolean
     */
    public function isPrivate()
    {
        return ( ( $this->_modifiers & self::IS_PRIVATE ) === self::IS_PRIVATE );
    }

    /**
     * @return boolean
     */
    public function isProtected()
    {
        return ( ( $this->_modifiers & self::IS_PROTECTED ) === self::IS_PROTECTED );
    }

    /**
     * @return boolean
     */
    public function isPublic()
    {
        return ( ( $this->_modifiers & self::IS_PUBLIC ) === self::IS_PUBLIC );
    }

    /**
     * Returns <b>true</b> when the reflected method/function is flagged as
     * deprecated.
     *
     * @return boolean
     */
    public function isDeprecated()
    {
        return false;
    }

    /**
     * Returns <b>true</b> when the reflected method is declared by an internal
     * class/interface, or <b>false</b> when it is user-defined.
     *
     * @return boolean
     */
    public function isInternal()
    {
        return false;
    }

    /**
     * Returns <b>true</b> when the reflected method is user-defined, otherwise
     * this method will return <b>false</b>.
     *
     * @return boolean
     */
    public function isUserDefined()
    {
        return true;
    }

    /**
     * Returns <b>true</b> when the reflected method/function is a closure,
     * otherwise this method will return <b>false</b>.
     *
     * @return boolean
     */
    public function isClosure()
    {
        return false;
    }

    /**
     * Gets the declaring class.
     *
     * @return \ReflectionClass
     */
    public function getDeclaringClass()
    {
        return $this->_declaringClass;
    }

    /**
     * Sets the <b>ReflectionClass</b> where the reflected method is declared.
     *
     * @param \ReflectionClass $declaringClass The class where the reflected
     *        method is declared.
     *
     * @return void
     * @access private
     */
    public function initDeclaringClass( \ReflectionClass $declaringClass )
    {
        if ( $this->_declaringClass === null )
        {
            $this->_declaringClass = $declaringClass;
        }
        else
        {
            throw new \LogicException( 'Declaring class already set' );
        }
    }

    /**
     * Returns the start line number of the reflected method's declaration.
     *
     * @return integer
     */
    public function getStartLine()
    {
        return $this->_startLine;
    }

    /**
     * Initializes the start line number where the method's declaration starts.
     *
     * @param integer $startLine The methods start line number.
     *
     * @return void
     * @access private
     */
    public function initStartLine( $startLine )
    {
        if ( $this->_startLine === -1 )
        {
            $this->_startLine = $startLine;
        }
        else
        {
            throw new \LogicException( 'Property startLine already set' );
        }
    }

    /**
     * Returns the end line number of the reflected method's declaration.
     *
     * @return integer
     */
    public function getEndLine()
    {
        return $this->_endLine;
    }

    /**
     * Initializes the end line number where the method's declaration ends.
     *
     * @param integer $endLine The methods end line number.
     *
     * @return void
     * @access private
     */
    public function initEndLine( $endLine )
    {
        if ( $this->_endLine === -1 )
        {
            $this->_endLine = $endLine;
        }
        else
        {
            throw new \LogicException( 'Property endLine already set' );
        }
    }

    /**
     * Returns an <b>array</b> with all parameters of the reflected method.
     *
     * @return array(\ReflectionParameter)
     */
    public function getParameters()
    {
        return (array) $this->_parameters;
    }

    /**
     * Returns the total number of parameters for the reflected method.
     *
     * @return integer
     */
    public function getNumberOfParameters()
    {
        return count( $this->getParameters() );
    }

    public function getNumberOfRequiredParameters()
    {

    }

    /**
     * Initializes the parameters declared for the reflected method.
     *
     * @param array(\org\pdepend\reflection\StaticReflectionParameter) $parameters
     *        Allowed parameters for the reflected method.
     *
     * @return void
     * @access private
     */
    public function initParameters( array $parameters )
    {
        if ( $this->_parameters === null )
        {
            $this->_initParameters( $parameters );
        }
        else
        {
            throw new \LogicException( 'Property parameters already set' );
        }
    }

    /**
     * Initializes the parameters declared for the reflected method.
     *
     * @param array(\org\pdepend\reflection\StaticReflectionParameter) $parameters
     *        Allowed parameters for the reflected method.
     *
     * @return void
     */
    private function _initParameters( array $parameters )
    {
        $this->_parameters = array();
        foreach ( $parameters as $parameter )
        {
            $parameter->initDeclaringMethod( $this );
            $this->_parameters[] = $parameter;
        }
    }

    public function returnsReference()
    {
        
    }

    public function getStaticVariables()
    {
        
    }

    /**
     * Returns a <b>\ReflectionExtension</b> of the extension where the reflected
     * method was declared. If the method is not part of an extension this
     * method will return <b>null</b>.
     *
     * @return \ReflectionExtension
     */
    public function getExtension()
    {
        return null;
    }

    /**
     * Returns the name of the extension where the reflected method was
     * declared. When the reflected method does not belong to an extension this
     * method will return <b>false</b>.
     *
     * @return string|boolean
     */
    public function getExtensionName()
    {
        return false;
    }

    /**
     * Will invoke the reflected method on the given <b>$object</b>.
     *
     * @param object $object The context object instance.
     * @param mixed  $args   Variable list of method arguments.
     *
     * @return void
     */
    public function invoke( $object, $args = null )
    {
        throw new \ReflectionException( 'Method invoke() is not supported' );
    }

    /**
     * Will invoke the reflected method on the given <b>$object</b>.
     *
     * @param object       $object The context object instance.
     * @param array(mixed) $args   Array with method arguments
     *
     * @return void
     */
    public function invokeArgs( $object, array $args = array() )
    {
        throw new \ReflectionException( 'Method invokeArgs() is not supported' );
    }

    /**
     * Returns the prototype of the context function.
     */
    public function getPrototype()
    {
        
    }

    /**
     * Returns a string representation of the reflected method.
     *
     * @return string
     */
    public function __toString()
    {
        return '';
    }
}