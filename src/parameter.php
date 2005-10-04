<?php
// {{{ DOC file

/**
 * File containing the ezcConsoleParameter class.
 *
 * @package ConsoleTools
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 */

// }}}

// {{{ DOC class

/**
 * Class for handling console parameters.
 * This class allows the complete handling of parameters submitted
 * to a console based application.
 *
 * <code>
 *
 * $paramHandler = new ezcConsoleParameter();
 * 
 * $help = array(
 *  'short' => 'Get help output.',
 *  'long'  => 'Retreive help on the usage of this command.',
 * );
 * $paramHandler->registerParam('h', 'help', $help);
 *
 * $file = array(
 *  'type'     => ezcConsoleParameter::TYPE_STRING
 *  'short'    => 'Process a file.',
 *  'long'     => 'Processes a single file.',
 *  'excludes' => array('d'),
 * )
 * $paramHandler->registerParam('f', 'file', $file);
 *
 * $dir = array(
 *  'type'     => ezcConsoleParameter::TYPE_STRING
 *  'short'    => 'Process a directory.',
 *  'long'     => 'Processes a complete directory.',
 *  'excludes' => array('f'),
 * )
 * $paramHandler->registerParam('d', 'dir', $dir);
 *
 * $paramHandler->registerAlias('d', 'directory', 'd');
 *
 * try {
 *      $paramHandler->processParams();
 * } catch (ezcConsoleParameterException $e) {
 *      if ($e->code === ezcConsoleParameterException::CODE_DEPENDENCY) {
 *          $consoleOut->outputText(
 *              'Parameter '.$e->paramName." may not occur here.\n", 'error'
 *          );
 *      }
 *      exit(1);
 * }
 *
 * </code>
 * 
 * @package ConsoleTools
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 */

// }}}
class ezcConsoleParameter
{

    // {{{ class constants

    /**
     * Parameter does not cary a value.
     */
    const TYPE_NONE     = 1;

    /**
     * Parameter takes an integer value.
     */
    const TYPE_INT      = 2;

    /**
     * Parameter takes a string value. 
     */
    const TYPE_STRING   = 3;

    // }}}

    
    // {{{ $paramDefs

    /**
     * Array of parameter definitions, indexed by number.
     * This array contains the paremeter definitions (short name, long name and
     * options) assigned to a number index. This index gets referenced by the
     * {@link ezcConsoleParameter::$paramShort} and 
     * {@link ezcConsoleParameter::$paramLong} arrays, which are indexed by the specific
     * parameter name.
     * 
     * @var array(int => array)
     */
    private $paramDefs = array();

    // }}}
    // {{{ $paramShort

    /**
     * Short paraemeter names. Each references a key in 
     * {@link ezcConsoleParameter::$paramDefs}.
     * 
     * @var array(string => int)
     */
    private $paramShort = array();

    // }}}
    // {{{ $paramLong

    /**
     * Long paraemeter names. Each references a key in 
     * {@link ezcConsoleParameter::$paramDefs}.
     * 
     * @var array(string => int)
     */
    private $paramLong = array();

    // }}}
    // {{{ $paramValues

    /**
     * Values submitted for a parameter, indexed by the key used for
     * {ezcConsoleParameter::$paramDefs}.
     * 
     * @var array(int => mixed)
     */
    private $paramValues = array();

    // }}}
    // {{{ $defaults

    /**
     * Default values for parameter options. 
     * 
     * @var array(string => mixed)
     */
    private $defaults = array( 
        'type'      => ezcConsoleParameter::TYPE_NONE,
        'default'   => null,
        'multiple'  => false,
        'short'     => '',
        'long'      => '',
        'depends'   => array(),
        'excludes'  => array(),
        'arguments' => true,
    );

    // }}}

    // {{{ __construct()

    /**
     * Create parameter handler
     */
    public function __construct() {
        
    }

    // }}}

    // {{{ registerParam()

    /**
     * Register a new parameter.
     * Register a new parameter to be recognized by the parser. The short 
     * option is a single character, the long option can be any string 
     * containing [a-z-]+. Via the options array several options can be 
     * defined for a parameter:
     *
     * <code>
     * array(
     *  'type'      => TYPE_NONE,  // option does not expect a value by 
     *                             // default, use TYPE_* constants
     *  'default'   => null,       // no default value by default
     *  'multiple'  => false,      // are multiple values expected?
     *  'short'     => '',         // no short description by default
     *  'long'      => '',         // no help text by default
     *  'depends'   => array(),    // no depending options by default
     *  'excludes'  => array(),    // no excluded options by default
     *  'arguments' => true,       // are arguments allowed?
     * );
     * </code>
     *
     * Attention: Already existing parameter will be overwriten! If an 
     * already existing alias is attempted to be registered, the alias 
     * will be deleted and replaced by the new parameter.
     *
     * Parameter shortcuts may only contain one character and will be 
     * used in an application call using "-x <value>". Long parameter
     * versions will be used like "--long-parameter=<value>".
     *
     * A parameter can have no value (TYPE_NONE), an integer/string
     * value (TYPE_INT/TYPE_STRING) or multiple of those 
     * ('muliple' => true).
     *
     * A parameter can also include a rule that disallows arguments, when
     * it's used. Per default arguments are allowed and can be retrieved
     * using the {ezcConsoleParameter::getArguments()} method.
     *
     * @see ezcConsoleParameter::unregisterParam()
     *
     * @param string $short          Short parameter
     * @param string $long           Long version of parameter
     * @param array(string) $options See description
     *
     * @return void
     */
    public function registerParam( $short, $long, $options = array() ) {
        end( $this->paramDefs );
        $nextKey = key( $this->paramDefs ) + 1;
        $this->paramDefs[$nextKey] = array( 
            'long'    => $long,
            'short'   => $short,
            'options' => array_merge( $this->defaults, $options ),
        );
        $this->paramShort[$short] = $nextKey;
        $this->paramLong[$long] = $nextKey;
    }

    // }}}
    // {{{ registerAlias()

    /**
     * Register an alias to a parameter.
     * Registers a new alias for an existing parameter. Aliases may
     * then be used as if they were real parameters.
     *
     * @see ezcConsoleParameter::unregisterAlias()
     *
     * @param string $short    Shortcut of the alias
     * @param string $long     Long version of the alias
     * @param strung $refShort Reference to an existing param (short)
     *
     * @return void
     *
     * @throws ezcConsoleParameterException
     * @see ezcConsoleParameterException::CODE_EXISTANCE
     */
    public function registerAlias( $short, $long, $refShort ) {
        if ( !isset( $this->paramShort[$refShort] ) ) {
            throw new ezcConsoleParameterException( 
                'Unknown parameter reference "' . $refShort . '".', 
                ezcConsoleParameterException::CODE_EXISTANCE, 
                $refShort 
            );
        }
        $this->paramShort[$short] = $this->paramShort[$refShort];
        $this->paramLong[$long] = $this->paramShort[$refShort];
    }

    // }}}
    // {{{ fromString()

    /**
     * Registeres parameters according to a string specification.
     * Accepts a string like used in eZ publis 3.x to define parameters and
     * registeres all parameters accordingly. String definitions look like
     * this:
     *
     * <code>
     * [s:|size:][u:|user:][a:|all:]
     * </code>
     *
     * This string will result in 3 parameters:
     * -s / --size
     * -u / --user
     * -a / --all
     *
     * @param string $paramDef Parameter definition string.
     * @throws ezcConsoleParameterException If string is not wellformed.
     *
     * @todo Implement.
     */
    public function fromString( $paramDef ) {
        
    }

    // }}}
    // {{{ unregisterParam()

    /**
     * Remove a parameter to be no more supported.
     * Using this function you will remove a parameter. Depending on the second 
     * option dependencies to this parameter are handled. Per default, just 
     * all dependencies to that actual parameter are removed (false value). 
     * Setting it to true will completely unregister all parameters that depend 
     * on the current one.
     *
     * @see ezcConsoleParameter::registerParam()
     *
     * @param string $short Short option name for the parameter to be removed.
     * @param bool $deps    Handling of dependencies while unregistering. 
     *
     * @return void
     *
     * @throws ezcConsoleParameterException 
     *         If requesting a nonexistant parameter 
     *         {@link ezcConsoleParameterException::CODE_EXISTANCE}.
     *
     * @todo Implement dependency check.
     */
    public function unregisterParam( $short, $deps = false ) {
        if ( !isset( $this->paramShort[$short] ) ) {
            throw new ezcConsoleParameterException( 
                'Unknown parameter reference "' . $short . '".', 
                ezcConsoleParameterException::CODE_EXISTANCE, 
                $short 
            );
        }
        $defKey = $this->paramShort[$short];
        // Unset long reference
        unset( $this->paramLong[$this->paramDefs[$defKey]['long']] );
        // Unset short reference
        unset( $this->paramShort[$short] );
        // Unset parameter definition itself
        unset( $this->paramDefs[$defKey] );
    }

    // }}}

    // {{{ getParamDef()

    /**
     * Returns the options defined for a specific parameter.
     * This method receives the long or short name of a parameter and
     * returns the options associated with it.
     * 
     * @param string $param Short or long name of the parameter.
     * @return array(string) Options set for the parameter.
     */
    public function getParamDef( $paramName )
    {
        if ( isset( $this->paramShort[$paramName] ) ) 
        {
            return $this->paramDefs[$this->paramShort[$paramName]]['options'];
        }
        if ( isset( $this->paramLong[$paramName] ) )
        {
            return $this->paramDefs[$this->paramLong[$paramName]]['options'];
        }
        throw new ezcConsoleParameterException( 
            'Unknown parameter reference "' . $paramName . '".', 
            ezcConsoleParameterException::CODE_EXISTANCE,
            $paramName
        );
    }

    // }}}

    // {{{ process()

    /**
     * Process the input parameters.
     * Actually process the input parameters according to the actual settings.
     * 
     * Per default this method uses $argc and $argv for processing. You can 
     * override this setting with your own input, if necessary, using the
     * parameters of this method. (Attention, first argument is always the pro
     * gram name itself!)
     *
     * All exceptions thrown by this method contain an additional attribute "param"
     * which specifies the parameter on which the error occured.
     * 
     * @param array(int -> string) $args The arguments
     *
     * @throws ezcConsoleParameterDependecyException 
     *         If dependencies are unmet 
     *         {@link ezcConsoleParameterException::CODE_DEPENDENCY}.
     * @throws ezcConsoleParameterExclusionException 
     *         If exclusion rules are unmet 
     *         {@link ezcConsoleParameterException::CODE_EXCLUSION}.
     * @throws ezcConsoleParameterTypeException 
     *         If type rules are unmet 
     *         {@link ezcConsoleParameterException::CODE_TYPE}.
     * 
     * @see ezcConsoleParameterException
     */ 
    public function process( $args = null ) {
        
    }

    // }}}
    
    // {{{ getParam()

    /**
     * Receive the data for a specific parameter.
     * Returns the data sumbitted for a specific parameter.
     *
     * @param string $short The parameter shortcut
     *
     * @return mixed String value of the parameter, true if set without 
     *               value or false on not set.
     */
    public function getParam( $short ) {
        if ( isset( $this->paramValues[$short] ) )
        {
            return $this->paramValues[$short];
        }
        return false;
    }

    // }}}
    // {{{ getArguments()

    /**
     * Returns arguments provided to the program.
     * This method returns all arguments provided to a program in an
     * integer indexed array. Arguments are sorted in the way
     * they are submitted to the program. You can disable arguments
     * through the 'arguments' flag of a parameter, if you want
     * to disallow arguments.
     *
     * Arguments are either the last part of the program call (if the
     * last parameter is not a 'multiple' one) or divided via the '--'
     * method which is commonly used on Unix (if the last parameter
     * accepts multiple values this is required).
     *
     * @return array(int => string) Arguments.
     */
    public function getArguments() {
        
    }

    // }}}
    // {{{ getHelp()

    /**
     * Returns array of help info on parameters.
     * If given a parameter shortcut, returns an array of several 
     * help information:
     *
     * <code>
     * array(
     *  'short' => <string>,
     *  'long'  => <string>,
     *  'usage' => <string>, // Autogenerated from the rules for the parameter
     *  'alias' => <string>, // Info on the aliases of a parameter
     * );
     * </code>
     *
     * If no parameter shortcut given, returns an array of above described 
     * arrays with a key for every parameter shortcut defined.
     * 
     * @param string $short Short cut value of the parameter.
     * @return array(string) See description.
     * 
     * @throws ezcConsoleParameterException 
     *         If requesting a nonexistant parameter 
     *         {@link ezcConsoleParameterException::CODE_EXISTANCE}.
     */
    public function getHelp( $short = null ) {

    }

    // }}}
    // {{{ getHelpText()

    /**
     * Returns string of help info on parameters.
     * If given a parameter shortcut, returns a string of help information:
     *
     * <code>
     * 
     * Usage: -<short> / --<long>= <type> <usageinfo>
     * <shortdesc>
     * <longdesc>
     * <dependencies> / <exclusions>
     *
     * </code>
     *
     * If not given a parameter shortcut, returns a string of global help information:
     *
     * <code>
     * 
     * Usage: [-<short>] [-<short>] ...
     * -<short> / --<long>  <type>  <default>   <shortdesc>
     * ...
     * 
     * </code>
     * 
     * @param string $short Shortcut of the parameter to get help text for.

     * @return string See description.
     * 
     * @throws ezcConsoleParameterException 
     *         If requesting a nonexistant parameter 
     *         {@link ezcConsoleParameterException::CODE_EXISTANCE}.
     */
    public function getHelpText( $short = null ) {
        
    }

    // }}}
    // {{{ getDefaults()

    /**
     * Return the default values for parameter options.
     * 
     * @return array(string => mixed)
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    // }}}

}
