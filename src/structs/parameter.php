<?php
/**
 * File containing the ezcConsoleParameterStruct class.
 *
 * @package ConsoleTools
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Struct class to store data about a single parameter handled by ezcConsoleParameter.
 * This class represents a single parameter which can be handled by the
 * ezcConsoleParameter class. This classes only purpose is the storage of
 * the parameter data.
 * 
 * @package ConsoleTools
 * @version //autogen//
 */
class ezcConsoleParameterStruct {

    /**
     * Short name of the parameter (not prefixed with '-').
     * 
     * @var string
     */
    public $short;

    /**
     * Long name of the parameter (not prefixed with '-').
     * 
     * @var string
     */
    public $long;

    /**
     * Value type of this parameter, default is ezcConsoleParameter::TYPE_NONE.
     * @see ezcConsoleParameter::TYPE_NONE
     * @see ezcConsoleParameter::TYPE_INT
     * @see ezcConsoleParameter::TYPE_STRING
     * 
     * @var int
     */
    public $type = ezcConsoleParameter::TYPE_NONE;

    /**
     * Default value if the parameter is submitted without value.
     * If a parameter is eg. of type ezcConsoleParameter::TYPE_STRING and 
     * therefore expects a value when being submitted, it may be submitted
     * without a value and automatically get the default value sepcified here.
     * 
     * @var mixed
     */
    public $default;

    /**
     * Short help text. Ususally displayed when showing parameter help overview.
     * 
     * @var string
     */
    public $shorthelp = 'No help available.';
    
    /**
     * Long help text. Ususally displayed when showing parameter detailed help.
     * 
     * @var string
     */
    public $longhelp = 'Sorry, there is no help on this topic available.';

    /**
     * Dependencies to other parameters, this parameter relies on.
     * 
     * @var array(string=>array)
     */
    public $depends = array();

    /**
     * Exclusions to other parameters, this parameter relies on.
     * 
     * @var array(string=>array)
     */
    public $excludes = array();

    /**
     * Whether arguments to the program are allowed, when this parameter is submitted. 
     * 
     * @var bool
     */
    public $arguments = true;

    /**
     * Create a new parameter struct.
     * Creates a new basic parameter struct with the base information "$short"
     * (the short name of the parameter) and "$long" (the long version). You
     * simply apply these parameters as strings (without '-' or '--'). So
     *
     * <code>
     * $param = new ezcConsoleParameterStruct('f', 'file');
     * </code>
     *
     * will result in a parameter that can be accessed using
     * 
     * <code>
     * $ mytool -f
     * </code>
     *
     * or
     * 
     * <code>
     * $ mytool --file
     * </code>
     * .
     *
     * The newly created parameter contains only it's 2 names and each other 
     * attribute is set to it's default value. You can simply manipulate
     * those attributes by accessing them directly.
     * 
     * @param string $short Short name of the parameter without '-' (eg. 'f').
     * @param string $long  Long name of the parameter without '--' (eg. 'file').
     */
    public function __construct( $short, $long )
    {
        $this->short = $short;
        $this->long = $long;
    }

    /* Add a new dependency for a parameter.
     * This adds a new dependency to a parameter. First parameter to this
     * method is the name of the parameter a dependency should be created
     * to. The second one specifies optionally an array of values that
     * the dependency relies on (e.g. $param->addDependency('a',
     * array('foo', 'bar')); will make the parameter depend on another
     * parameter '-a' to be set to 'foo' or to 'bar'). If the second
     * parameter is left out, it means that only '-a' must be set, no matter
     * with which value.
     *
     * @param string $param Short or longname of the parameter to add a 
     *                      dependency to (without '-' and '--'!).
     * @param array $values Optional values the depending parameter may take
     *                      or null to allow any value.
     */
    public function addDependency( $param, $values = true )
    {
        if ( !isset( $this->dependends[$param] ) )
        {
            $this->depends[$name] = $values;
        }
    }
    
    /**
     * Returns the dependencies of a parameter.
     * Returns an array containing all dependencies registered with this
     * paramerter. The array is indexed by the short name of the depending
     * parameters, which are assigned to a) an array of possible values or
     * b) true to indicate that the parameter is simply required without any
     * special value.
     *
     * For example:
     * <code>
     * array(
     *    'a' => true,                  // Parameter -a must  be set
     *    'b' => array('foo'),          // Parameter -b must be 'foo'
     *    'c' => array('foo', 'bar'),   // Parameter -c must be 'foo' or 'bar'
     * );
     * </code>
     * 
     * @return array Dependency definition as described or an empty array.
     */
    public function getDependencies()
    {
        return $this->depends;
    }

    /**
     * Reset existing dependencies.
     * Deletes all existing dependencies from the parameter definition and
     * resets the dependency field back to it's initial status.
     */
    public function resetDependencies() 
    {
        $this->depends = array();
    }
    
    /* Add a new exclusion for a parameter.
     * This adds a new exclusion to a parameter. First parameter to this
     * method is the name of the parameter a exclusion should be created
     * to. The second one specifies optionally an array of values that
     * the exclusion relies on (e.g. $param->addExclusion('a',
     * array('foo', 'bar')); will make the parameter exclude another
     * parameter '-a' which may not be set to 'foo' or to 'bar'). If the second
     * parameter is left out, it means that '-a' must not be set at all, no matter
     * with which value.
     *
     * @param string $param Short or longname of the parameter to add a 
     *                      exclusion to (without '-' and '--'!).
     * @param array $values Optional values the excluded parameter must not take
     *                      or null to disallow any value.
     */
    public function addExclusion( $param, $values = true )
    {
        if ( !isset( $this->dependends[$param] ) )
        {
            $this->excludes[$name] = $values;
        }
    }
    
    /**
     * Returns the exclusions of a parameter.
     * Returns an array containing all exclusions registered with this
     * paramerter. The array is indexed by the short name of the excluded
     * parameters, which are assigned to a) an array of disallowed values or
     * b) true to indicate that the parameter is simply excluded without any
     * special value restrictions.
     *
     * For example:
     * <code>
     * array(
     *    'a' => true,                  // Parameter -a may not  be set
     *    'b' => array('foo'),          // Parameter -b may not be 'foo'
     *    'c' => array('foo', 'bar'),   // Parameter -c may not be 'foo' or 'bar'
     * );
     * </code>
     * 
     * @return array Exclusion definition as described or an empty array.
     */
    public function getExlusions()
    {
        return $this->excludes;
    }

    /**
     * Reset existing exclusions.
     * Deletes all existing exclusions from the parameter definition and
     * resets the exclusion field back to it's initial status.
     */
    public function resetExclusions() 
    {
        $this->excludes = array();
    }
}

?>
