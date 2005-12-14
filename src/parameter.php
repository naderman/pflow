<?php
/**
 * File containing the ezcConsoleParameter class.
 *
 * @package ConsoleTools
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Class for handling console parameters.
 * This class allows the complete handling of parameters submitted
 * to a console based application.
 *
 * <code>
 * $paramHandler = new ezcConsoleParameter();
 * 
 * // Register simple parameter -h/--help
 * $paramHandler->registerParam( new ezcConsoleOption( 'h', 'help' ) );
 *
 * // Register complex parameter -f/--file
 * $file = new ezcConsoleOption(
 *  'f',
 *  'file',
 *  ezcConsoleParameter::TYPE_STRING,
 *  null,
 *  false,
 *  'Process a file.',
 *  'Processes a single file.',
 *  array(),
 *  array( new ezcConsoleOptionRule( $paramHandler->getParam( 'd' ) ) ),
 * )
 * $paramHandler->registerParam( $file );
 *
 * // Manipulate parameter -f/--file after registration
 * $file->multiple = true;
 * 
 * // Register another complex parameter
 * $dir = new ezcConsoleOption(
 *  'd',
 *  'dir',
 *  ezcConsoleParameter::TYPE_STRING,
 *  null,
 *  true,
 *  'Process a directory.',
 *  'Processes a complete directory.',
 *  'excludes' => array( new ezcConsoleOptionRule( $paramHandler->getParam( 'h' ) ) ),
 * )
 * $paramHandler->registerParam( $dir );
 *
 * // Register an alias for this parameter
 * $paramHandler->registerAlias( 'e', 'extended-dir', $dir );
 *
 * // Process registered parameters and handle errors
 * try
 * {
 *      $paramHandler->process();
 * }
 * catch ( ezcConsoleParameterException $e )
 * {
 *      if ( $e->code === ezcConsoleParameterException::PARAMETER_DEPENDENCY_RULE_NOT_MET )
 *      {
 *          $consoleOut->outputText(
 *              'Parameter ' . isset( $e->param ) ? $e->param->name : 'unknown' . " may not occur here.\n", 'error'
 *          );
 *      }
 *      exit( 1 );
 * }
 *
 * // Process a single parameter
 * $file = $paramHandler->getParam( 'f' );
 * if ( $file->value === false )
 * {
 *      echo "Parameter -{$file->short}/--{$file->long} was not submitted.\n";
 * }
 * elseif ( $file->value === true )
 * {
 *      echo "Parameter -{$file->short}/--{$file->long} was submitted without value.\n";
 * }
 * else
 * {
 *      echo "Parameter -{$file->short}/--{$file->long} was submitted with value <{$file->value}>.\n";
 * }
 *
 * // Process all parameters at once:
 * foreach ( $paramHandler->getValues() as $paramShort => $val )
 * {
 *      switch (true)
 *      {
 *          case $val === false:
 *              echo "Parameter $paramShort was not submitted.\n";
 *              break;
 *          case $val === true:
 *              echo "Parameter $paramShort was submitted without a value.\n";
 *              break;
 *          case is_array($val):
 *              echo "Parameter $paramShort was submitted multiple times with value: <".implode(', ', $val).">.\n";
 *              break;
 *          default:
 *              echo "Parameter $paramShort was submitted with value: <$val>.\n";
 *              break;
 *      }
 * }
 * </code>
 * 
 * @package ConsoleTools
 * @version //autogen//
 */
class ezcConsoleParameter
{
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

    /**
     * Array of parameter definitions, indexed by number.
     * This array stores the ezcConsoleOption objects representing
     * the parameters.
     *
     * For lookup of a parameter after it's short or long values the attributes
     * @link ezcConsoleParameter::$paramShort
     * @link ezcConsoleParameter::$paramLong
     * are used.
     * 
     * @var array(int => array)
     */
    private $params = array();

    /**
     * Short paraemeter names. Each references a key in 
     * {@link ezcConsoleParameter::$params}.
     * 
     * @var array(string => int)
     */
    private $paramShort = array();

    /**
     * Long paraemeter names. Each references a key in 
     * {@link ezcConsoleParameter::$params}.
     * 
     * @var array(string => int)
     */
    private $paramLong = array();

    /**
     * Arguments, if submitted, are stored here. 
     * 
     * @var array
     */
    private $arguments = array();

    /**
     * Create parameter handler
     */
    public function __construct()
    {
    }

    /**
     * Register a new parameter.
     * This method adds a new parameter to your parameter collection. If allready a
     * parameter with the assigned short or long value exists, an exception will
     * be thrown.
     *
     * @see ezcConsoleParameter::unregisterParam()
     *
     * @param ezcConsoleOption $param The parameter to register.
     *
     */
    public function registerParam( ezcConsoleOption $param )
    {
        foreach ( $this->paramShort as $short => $ref )
        {
            if ( $short === $param->short ) 
            {
                throw new ezcConsoleParameterException( 
                    "A parameter with the short name <{$short}> is already registered.",
                    ezcConsoleParameterException::PARAMETER_ALREADY_REGISTERED,
                    $param
                );
            }
        }
        foreach ( $this->paramLong as $long => $ref )
        {
            if ( $long === $param->long ) 
            {
                throw new ezcConsoleParameterException( 
                    "A parameter with the long name <{$long}> is already registered.",
                    ezcConsoleParameterException::PARAMETER_ALREADY_REGISTERED,
                    $param
                );
            }
        }
        $this->params[] = $param;
        $this->paramLong[$param->long] = $param;
        $this->paramShort[$param->short] = $param;
    }

    /**
     * Register an alias to a parameter.
     * Registers a new alias for an existing parameter. Aliases may
     * then be used as if they were real parameters.
     *
     * @see ezcConsoleParameter::unregisterAlias()
     *
     * @param string $short                    Shortcut of the alias
     * @param string $long                     Long version of the alias
     * @param ezcConsoleOption $param Reference to an existing parameter
     *
     *
     * @throws ezcConsoleParameterException
     *         If the referenced parameter does not exist
     *         {@link ezcConsoleParameterException::PARAMETER_NOT_EXISTS}.
     * @throws ezcConsoleParameterException
     *         If another parameter/alias has taken the provided short or long name
     *         {@link ezcConsoleParameterException::PARAMETER_ALREADY_REGISTERED}.
     */
    public function registerAlias( $short, $long, $param )
    {
        $short = ezcConsoleOption::sanitizeParameterName($short);
        $long = ezcConsoleOption::sanitizeParameterName($long);
        if ( !isset( $this->paramShort[$param->short] ) || !isset( $this->paramLong[$param->long] ) )
        {
            throw new ezcConsoleParameterException( 
                "The referenced parameter <{$param->short}>/<{$param->long}> is not registered so <{$short}>/<{$long}> cannot be made an alias.",
                ezcConsoleParameterException::PARAMETER_NOT_EXISTS,
                $param
            );
        }
        if ( isset( $this->paramShort[$short] ) || isset( $paramLong[$long] ) )
        {
            throw new ezcConsoleParameterException( 
                "The parameter <{$short}>/<{$long}> does already exist.",
                ezcConsoleParameterException::PARAMETER_ALREADY_REGISTERED,
                isset( $this->paramShort[$short] ) ? $this->paramShort[$short] : $this->paramLong[$long]
            );
        }
        $this->shortParam[$short] = $param;
        $this->longParam[$long] = $param;
    }

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
     * 
     * @throws ezcConsoleParameterException 
     *         If string is not wellformed
     *         {@link ezcConsoleParameterException::PARAMETER_STRING_NOT_WELLFORMED}.
     */
    public function fromString( $paramDef ) 
    {
        $regex = '/\[([a-z0-9-]+)([:?*+])?([^|]*)\|([a-z0-9-]+)([:?*+])?\]/';
        if ( preg_match_all( $regex, $paramDef, $matches ) )
        {
            foreach ( $matches[1] as $id => $short )
            {
                $param = null;
                if ( empty( $matches[4][$id] )  ) 
                {
                    throw new ezcConsoleParameterException( 
                        "Missing long parameter name for short parameter <-{$short}>",
                        ezcConsoleParameterException::PARAMETER_STRING_NOT_WELLFORMED 
                    );
                }
                $param = new ezcConsoleOption($short, $matches[4][$id]);
                if ( !empty( $matches[2][$id] ) || !empty( $matches[5][$id] ) )
                {
                    switch ( !empty( $matches[2][$id] ) ? $matches[2][$id] : $matches[5][$id] )
                    {
                        case '*':
                            // Allows 0 or more occurances
                            $param->multiple = true;
                            break;
                        case '+':
                            // Allows 1 or more occurances
                            $param->multiple = true;
                            $param->type = self::TYPE_STRING;
                            break;
                        case '?':
                            $param->type = self::TYPE_STRING;
                            $param->default = '';
                            break;
                        default:
                            break;
                    }
                }
                if ( !empty( $matches[3][$id] ) )
                {
                    $param->default = $matches[3][$id];
                }
                $this->registerParam( $param );
            }
        }

    }

    /**
     * Remove a parameter to be no more supported.
     * Using this function you will remove a parameter. All dependencies to that 
     * specific parameter are removed completly from every other registered 
     * parameter.
     *
     * @see ezcConsoleParameter::registerParam()
     *
     * @throws ezcConsoleParameterException 
     *         If requesting a nonexistant parameter
     *         {@link ezcConsoleParameterException::PARAMETER_NOT_EXISTS}.
     */
    public function unregisterParam( $param )
    {
        $found = false;
        foreach ( $this->params as $id => $existParam )
        {
            if ( $existParam === $param )
            {
                $found = true;
                unset($this->params[$id]);
                continue;
            }
            $existParam->removeAllExclusions($param);
            $existParam->removeAllDependencies($param);
        }
        if ( $found === false )
        {
            throw new ezcConsoleParameterException( 
                "The referenced parameter <{$param->short}>/<{$param->long}> is not registered.",
                ezcConsoleParameterException::PARAMETER_NOT_EXISTS,
                $param
            );
        }
        foreach ( $this->paramLong as $name => $existParam )
        {
            if ( $existParam === $param )
            {
                unset($this->paramLong[$name]);
            }
        }
        foreach ( $this->paramShort as $name => $existParam )
        {
            if ( $existParam === $param )
            {
                unset($this->paramShort[$name]);
            }
        }
    }
    
    /**
     * Remove a alias to be no more supported.
     * Using this function you will remove an alias.
     *
     * @see ezcConsoleParameter::registerAlias()
     * 
     * @throws ezcConsoleParameterException
     *      If the requested short/long name belongs to a real parameter instead
     *      of an alias {@link ezcConsoleParameterException::PARAMETER_IS_NO_ALIAS}. 
     *
     * @param mixed $short 
     * @param mixed $long 
     */
    public function unregisterAlias( $short, $long )
    {
        $short = ezcConsoleOption::sanitizeParameterName($short);
        $long = ezcConsoleOption::sanitizeParameterName($long);
        foreach ( $this->params as $id => $param )
        {
            if ( $param->short === $short )
            {
                throw new ezcConsoleParameterException( 
                    "The short name <{$short}> refers to a real parameter, not to an alias.",
                    ezcConsoleParameterException::PARAMETER_IS_NO_ALIAS,
                    $param
                );
            }
            if ( $param->long === $long )
            {
                throw new ezcConsoleParameterException( 
                    "The long name <{$long}> refers to a real parameter, not to an alias.",
                    ezcConsoleParameterException::PARAMETER_IS_NO_ALIAS,
                    $param
                );
            }
        }
        if ( isset( $this->paramShort[$short] ) )
        {
            unset($this->paramShort[$short]);
        }
        if ( isset( $this->paramLong[$short] ) )
        {
            unset($this->paramLong[$long]);
        }
    }

    /**
     * Returns the options defined for a specific parameter.
     * This method receives the long or short name of a parameter and
     * returns the options associated with it.
     * 
     * @param string $name Short or long name of the parameter.
     * @return array(string) Options set for the parameter.
     *
     * @throws ezcConsoleParameterException 
     *         If requesting a nonexistant parameter
     *         {@link ezcConsoleParameterException::PARAMETER_NOT_EXISTS}.
     */
    public function getParam( $name )
    {
        $name = ezcConsoleOption::sanitizeParameterName($name);
        if ( isset( $this->paramShort[$name] ) )
        {
            return $this->paramShort[$name];
        }
        if ( isset( $this->paramLong[$name] ) )
        {
            return $this->paramLong[$name];
        }
        throw new ezcConsoleParameterException( 
            "<{$name}> is not a valid parameter long or short name.", 
            ezcConsoleParameterException::PARAMETER_NOT_EXISTS,
            null
        );
    }

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
     * @throws ezcConsoleParameterException 
     *         If dependencies are unmet 
     *         {@link ezcConsoleParameterException::PARAMETER_DEPENDENCY_RULE_NOT_MET}.
     * @throws ezcConsoleParameterException 
     *         If exclusion rules are unmet 
     *         {@link ezcConsoleParameterException::PARAMETER_EXCLUSION_RULE_NOT_MET}.
     * @throws ezcConsoleParameterException 
     *         If type rules are unmet 
     *         {@link ezcConsoleParameterException::PARAMETER_TYPE_RULE_NOT_MET}.
     * @throws ezcConsoleParameterException 
     *         If a parameter used does not exist
     *         {@link ezcConsoleParameterException::PARAMETER_NOT_EXISTS}.
     * @throws ezcConsoleParameterException 
     *         If arguments are passed although a parameter dissallowed them
     *         {@link ezcConsoleParameterException::ARGUMENTS_NOT_ALLOWED}.
     * 
     * @see ezcConsoleParameterException
     */ 
    public function process( $args = null )
    {
        if ( !isset( $args ) )
        {
            $args = isset( $argv ) ? $argv : isset( $_SERVER['argv'] ) ? $_SERVER['argv'] : array();
        }
        $i = 1;
        while ( $i < count( $args ) )
        {
            // Equalize parameter handling (long params with =)
            if ( substr( $args[$i], 0, 2 ) == '--' )
            {
                $this->preprocessLongParam( $args, $i );
            }
            // Check for parameter
            if ( substr( $args[$i], 0, 1) === '-' && $this->parameterExists( $args[$i] ) !== false )
            {
                $this->processParameter( $args, $i );
            }
            // Looks like parameter, but is not available??
            elseif ( substr( $args[$i], 0, 1) === '-' && trim( $args[$i] ) !== '--' )
            {
                throw new ezcConsoleParameterException(
                    "Unknown parameter <{$args[$i]}>.",
                    ezcConsoleParameterException::PARAMETER_NOT_EXISTS,
                    null
                );
            }
            // Must be the arguments
            else
            {
                $args[$i] == '--' ? ++$i : $i;
                $this->processArguments( $args, $i );
                break;
            }
        }
        $this->checkRules();
    }

    /**
     * Returns if a parameter with the given name exists.
     * Checks if a parameter with the given name is registered.
     * 
     * @param string $name Short or long name of the parameter.
     * @return bool True if parameter exists, otherwise false.
     */
    public function parameterExists( $name )
    {
        try
        {
            $param = $this->getParam( $name );
        }
        catch ( ezcConsoleParameterException $e )
        {
            return false;
        }
        return true;
    }

    /**
     * Returns an array of all registered parameters.
     * Returns an array of all registered parameter in the following format:
     * <code>
     * array( 
     *      0 => object(ezcConsoleOption),
     *      1 => object(ezcConsoleOption),
     *      2 => object(ezcConsoleOption),
     *      ...
     * );
     * </code>
     *
     * @return array(string=>object(ezcConsoleOption)) Registered parameters.
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Returns all values submitted.
     * Returns an array of all values submitted to the parameters. The array is 
     * indexed by the parameters short name (excluding the '-' prefix).
     * 
     * @return array(string => mixed)
     */
    public function getValues()
    {
        $res = array();
        foreach ( $this->params as $param )
        {
            $res[$param->short] = $param->value;
        }
        return $res;
    }

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
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Get help information for your parameters.
     * This method returns an array of help information for your parameters,
     * indexed by integer. Each helo info has 2 fields:
     *
     * 0 => The parameters names ("<short> / <long>")
     * 1 => The help text (depending on the $long parameter)
     *
     * The $long parameter determines if you want to get the short- or longhelp
     * texts. The array returned can be used by {@link ezcConsoleTable}.
     *
     * If using the second parameter, you can filter the parameters shown in the
     * help output (e.g. to show short help for related parameters). Provide
     * as simple number indexed array of short and/or long values to set a filter.
     * 
     * @param bool $long Set this to true for getting the long help version.
     * @param array $params Set of parameters to generate help for, default is all.
     */
    public function getHelp( $long = false, $params = array() )
    {
        $help = array();
        foreach ( $this->params as $id => $param )
        {
            if ( count($params) === 0 || in_array( $param->short, $params ) || in_array( $param->long, $params ) )
            {
                $help[] = array( 
                    '-' . $param->short . ' / ' . '--' . $param->long,
                    $long == false ? $param->shorthelp : $param->longhelp,
                );
            }
        }
        return $help;
    }

    /**
     * Process a parameter.
     * This method does the processing of a single parameter. 
     * 
     * @param array $args The arguments array.
     * @param int $i      The current position in the arguments array.
     * @param int The current index in the $args array.
     */
    private function processParameter( $args, &$i )
    {
        $param = $this->getParam( $args[$i++] );
        // No value expected
        if ( $param->type === ezcConsoleParameter::TYPE_NONE )
        {
            // No value expected
            if ( isset( $args[$i] ) && substr( $args[$i], 0, 1 ) !== '-' )
            {
                // But one found
                throw new Exception( 
                    "Parameter with long name <{$param->long}> does not expect a value but <{$args[$i]}> was submitted.",
                    ezcConsoleParameterException::PARAMETER_TYPE_RULE_NOT_MET,
                    $param
                );
            }
            // Multiple occurance possible
            if ( $param->multiple === true )
            {
                $param->value[] = true;
            }
            else
            {
                $param->value = true;
            }
            // Everything fine, nothing to do
            return $i;
        }
        // Value expected, check for it
        if ( isset( $args[$i] ) && substr( $args[$i], 0, 1 ) !== '-' )
        {
            // Type check
            if ( $this->correctType( $param, $args[$i] ) === false )
            {
                throw new ezcConsoleParameterException( 
                    "Parameter with long name <{$param->long}> of incorrect type.",
                    ezcConsoleParameterException::PARAMETER_TYPE_RULE_NOT_MET,
                    $param
                );
            }
            // Multiple values possible
            if ( $param->multiple === true )
            {
                $param->value[] = $args[$i];
            }
            // Only single value expected, check for multiple
            elseif ( isset( $param->value ) && $param->value !== false )
            {
                throw new ezcConsoleParameterException( 
                    "Parameter with long name <{$param->long}> expects only 1 value but multiple have been submitted.",
                    ezcConsoleParameterException::TOO_MANY_PARAMETER_VALUES,
                    $param
                );
            }
            else
            {
                $param->value = $args[$i];
            }
            $i++;
        }
        // Value found? If not, use default, if available
        if ( !isset( $param->value ) || $param->value === false || ( is_array( $param->value ) && count( $param->value ) === 0) ) 
        {
            if ( isset( $param->default ) ) 
            {
                $param->value = $param->multiple === true ? array( $param->default ) : $param->default;
            }
            else
            {
                throw new ezcConsoleParameterException( 
                    "Parameter value missing for parameter with long name <{$param->long}>.",
                    ezcConsoleParameterException::MISSING_PARAMETER_VALUE,
                    $param
                );
            }
        }
        return $i;
    }

    /**
     * Process arguments given to the program. 
     * 
     * @todo FIXME: Add test for this!
     * @param array $args The arguments array.
     * @param int $i Current index in arguments array.
     */
    private function processArguments( $args, &$i )
    {
        while ( $i < count( $args ) )
        {
            $this->arguments[] = $args[$i++];
        }
    }

    /**
     * Check the rules that may be associated with a parameter.
     * Parameters are allowed to have rules associated for
     * dependencies to other parameters and exclusion of other parameters or
     * arguments. This method processes the checks.
     * 
     *
     * @throws ezcConsoleParameterException 
     *         If dependencies are unmet 
     *         {@link ezcConsoleParameterException::PARAMETER_DEPENDENCY_RULE_NOT_MET}.
     * @throws ezcConsoleParameterException 
     *         If exclusion rules are unmet 
     *         {@link ezcConsoleParameterException::PARAMETER_EXCLUSION_RULE_NOT_MET}.
     * @throws ezcConsoleParameterException 
     *         If arguments are passed although a parameter dissallowed them
     *         {@link ezcConsoleParameterException::ARGUMENTS_NOT_ALLOWED}.
     */
    private function checkRules()
    {
        $values = $this->getValues();
        foreach ( $this->params as $id => $param )
        {
            if ( $param->value === false || is_array( $param->value ) && count( $param->value ) === 0 )
            {
                // Parameter was not set so ignore it's rules.
                continue;
            }
            // Dependencies
            foreach ( $param->getDependencies() as $dep )
            {
                if ( !isset( $values[$dep->option->short] ) || $values[$dep->option->short] === false )
                {
                    throw new ezcConsoleParameterException( 
                        "Parameter with long name <{$param->long}> depends on parameter with long name <{$dep->option->long}> which was not submitted.",
                        ezcConsoleParameterException::PARAMETER_DEPENDENCY_RULE_NOT_MET,
                        $param
                    );
                }
                $depVals = $dep->values;
                if ( count( $depVals ) > 0 )
                {
                    if ( !in_array( $values[$dep->option->short], $depVals ) )
                    {
                        throw new ezcConsoleParameterException( 
                            "Parameter with long name <{$param->long}> depends on parameter with long name <{$dep->option->long}> to be in a specific value range, but isn't.",
                            ezcConsoleParameterException::PARAMETER_DEPENDENCY_RULE_NOT_MET,
                            $param
                        );
                    }
                }
            }
            // Exclusions
            foreach ( $param->getExclusions() as $exc )
            {
                if ( isset( $values[$exc->option->short] ) && $values[$exc->option->short] !== false )
                {
                    throw new ezcConsoleParameterException( 
                        "Parameter with long name <{$param->long}> excludes the parameter with long name <{$exc->option->long}> which was submitted.",
                        ezcConsoleParameterException::PARAMETER_EXCLUSION_RULE_NOT_MET,
                        $param
                    );
                }
                $excVals = $exc->values;
                if ( count( $excVals ) > 0 )
                {
                    if ( in_array( $values[$exc->option->short], $excVals ) )
                    {
                        throw new ezcConsoleParameterException( 
                            "Parameter with long name <{$param->long}> excludes parameter with long name <{$exc->option->long}> to be in a specific value range, but it is.",
                            ezcConsoleParameterException::PARAMETER_EXCLUSION_RULE_NOT_MET,
                            $param
                        );
                    }
                }
            }
            // Arguments
            if ( $param->arguments === false && is_array( $this->arguments ) && count( $this->arguments ) > 0 )
            {
                throw new ezcConsoleParameterException( 
                    "Parameter with long name <{$param->long}> excludes the usage of arguments, but arguments have been passed.",
                    ezcConsoleParameterException::ARGUMENTS_NOT_ALLOWED,
                    $param
                );
            }
        }
    }

    /**
     * Checks if a value is of a given type. Converts the value to the
     * correct PHP type on success.
     *  
     * @param int $param  The parameter.
     * @param string $val The value to check.
     * @return bool True on succesful check, otherwise false.
     */
    private function correctType( $param, &$val )
    {
        $res = false;
        switch ( $param->type )
        {
            case ezcConsoleParameter::TYPE_STRING:
                $res = true;
                $val = preg_replace( '/^(["\'])(.*)\1$/', '\2', $val );
                break;
            case ezcConsoleParameter::TYPE_INT:
                $res = preg_match( '/^[0-9]+$/', $val ) ? true : false;
                if ( $res )
                {
                    $val = (int)$val;
                }
                break;
        }
        return $res;
    }

    /**
     * Split parameter and value for long parameter names. This method checks 
     * for long parameters, if the value is passed using =. If this is the case
     * parameter and value get split and replaced in the arguments array.
     * 
     * @param array $args The arguments array
     * @param int $i Current arguments array position
     */
    private function preprocessLongParam( &$args, $i )
    {
        // Value given?
        if ( preg_match( '/^--\w+\=[^ ]/i', $args[$i] ) )
        {
            // Split param and value and replace current param
            $parts = explode( '=', $args[$i], 2 );
            array_splice( $args, $i, 1, $parts );
        }
    }
}
?>
