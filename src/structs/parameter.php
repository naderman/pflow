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
 *
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
     * Dependency rules of this parameter.
     * 
     * @see ezcConsoleParamemterStruct::addDependency()
     * @see ezcConsoleParamemterStruct::removeDependency()
     * @see ezcConsoleParamemterStruct::hasDependency()
     * @see ezcConsoleParamemterStruct::getDependencies()
     * @see ezcConsoleParamemterStruct::resetDependencies()
     * 
     * @var array(string=>ezcConsoleParamemterRule)
     */
    protected $dependencies = array();

    /**
     * Exclusion rules of this parameter.
     * 
     * @see ezcConsoleParamemterStruct::addExclusion()
     * @see ezcConsoleParamemterStruct::removeExclusion()
     * @see ezcConsoleParamemterStruct::hasExclusion()
     * @see ezcConsoleParamemterStruct::getExclusions()
     * @see ezcConsoleParamemterStruct::resetExclusions()
     * 
     * @var array(string=>ezcConsoleParamemterRule)
     */
    protected $exclusions = array();

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
     * This registeres a new dependency rule with the parameter. If you try
     * to add an already registered rule it will simply be ignored. Else,
     * the submitted rule will be added to the parameter as a dependency.
     *
     * @param ezcConsoleParameterRule $rule The rule to add.
     */
    public function addDependency( ezcConsoleParameterRule $rule )
    {
        foreach ( $this->dependencies as $existRule )
        {
            if ( $rule === $existRule )
            {
                return;
            }
        }
        $this->dependencies[] = $rule;
    }
    
    /**
     * Remove a dependency rule from a parameter.
     * This removes a given rule from a parameter, if it exists. If the rule is
     * not registered with the parameter, the method call will simply be ignored.
     * 
     * @param ezcConsoleParameterRule $rule The rule to be removed.
     */
    public function removeDependency( ezcConsoleParameterRule $rule )
    {
        foreach ( $this->dependencies as $id => $existRule )
        {
            if ( $rule === $existRule )
            {
                unset( $this->dependencies[$id] );
            }
        }
    }
    
    /**
     * Returns if a given dependency rule is registered with the parameter.
     * Returns true if the given rule is registered with this parameter,
     * otherwise false.
     * 
     * @param ezcConsoleParameterRule $rule The rule to be removed.
     * @returns bool True if rule is registered, otherwise false.
     */
    public function hasDependency( ezcConsoleParameterRule $rule )
    {
        foreach ( $this->dependencies as $id => $existRule )
        {
            if ( $rule === $existRule )
            {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Returns the dependency rules registered with this parameter.
     * Returns an array of registered dependencies.
     *
     * For example:
     * <code>
     * array(
     *      0 => object(ezcConsoleParameterRule),
     *      1 => object(ezcConsoleParameterRule),
     *      2 => object(ezcConsoleParameterRule),
     * );
     * </code>
     * 
     * @return array Dependency definition as described or an empty array.
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * Reset existing dependency rules.
     * Deletes all registered dependency rules from the parameter definition.
     */
    public function resetDependencies() 
    {
        $this->dependencies = array();
    }

    /* Add a new exclusion for a parameter.
     * This registeres a new exclusion rule with the parameter. If you try
     * to add an already registered rule it will simply be ignored. Else,
     * the submitted rule will be added to the parameter as a exclusion.
     *
     * @param ezcConsoleParameterRule $rule The rule to add.
     */
    public function addExclusion( ezcConsoleParameterRule $rule )
    {
        foreach ( $this->exclusions as $existRule )
        {
            if ( $rule === $existRule )
            {
                return;
            }
        }
        $this->exclusions[] = $rule;
    }
    
    /**
     * Remove a exclusion rule from a parameter.
     * This removes a given rule from a parameter, if it exists. If the rule is
     * not registered with the parameter, the method call will simply be ignored.
     * 
     * @param ezcConsoleParameterRule $rule The rule to be removed.
     */
    public function removeExclusion( ezcConsoleParameterRule $rule )
    {
        foreach ( $this->exclusions as $id => $existRule )
        {
            if ( $rule === $existRule )
            {
                unset( $this->exclusions[$id] );
            }
        }
    }
    
    /**
     * Returns if a given exclusion rule is registered with the parameter.
     * Returns true if the given rule is registered with this parameter,
     * otherwise false.
     * 
     * @param ezcConsoleParameterRule $rule The rule to be removed.
     * @returns bool True if rule is registered, otherwise false.
     */
    public function hasExclusion( ezcConsoleParameterRule $rule )
    {
        foreach ( $this->exclusions as $id => $existRule )
        {
            if ( $rule === $existRule )
            {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Returns the exclusion rules registered with this parameter.
     * Returns an array of registered exclusions.
     *
     * For example:
     * <code>
     * array(
     *      0 => object(ezcConsoleParameterRule),
     *      1 => object(ezcConsoleParameterRule),
     *      2 => object(ezcConsoleParameterRule),
     * );
     * </code>
     * 
     * @return array Exclusion definition as described or an empty array.
     */
    public function getExclusions()
    {
        return $this->exclusions;
    }

    /**
     * Reset existing exclusion rules.
     * Deletes all registered exclusion rules from the parameter definition.
     */
    public function resetExclusions() 
    {
        $this->exclusions = array();
    }
    
    
}

?>
