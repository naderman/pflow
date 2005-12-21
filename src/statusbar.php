<?php
/**
 * File containing the ezcConsoleStatusbar class.
 *
 * @package ConsoleTools
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Creating  and maintaining statusbars to be printed to the console. 
 *
 * <code>
 *
 * // Construction
 * $status = new ezcConsoleStatusbar( new ezcConsoleOutput() );
 *
 * // Set option
 * $status->successChar = '*';
 *
 * // Run statusbar
 * foreach ( $files as $file )
 * {
 *      $res = $file->upload();
 *      // Add status if form of bool true/false to statusbar.
 *      $status->add( $res ); // $res is true or false
 * }
 *
 * // Retreive and display final statusbar results
 * $msg = $status->getSuccess() . ' succeeded, ' . $status->getFailure() . ' failed.';
 * $out->outputText( "Finished uploading files: $msg\n" );
 *
 * </code>
 *  
 * 
 * @package ConsoleTools
 * @version //autogen//
 */
class ezcConsoleStatusbar
{
    /**
     * Options
     *
     * <code>
     * array(
     *   'successChar' => '+',     // Char to indicate success
     *   'failureChar' => '-',     // Char to indicate failure
     * );
     * </code>
     *
     * @var array(string)
     */
    protected $options = array(
        'successChar' => '+',     // Char to indicate success
        'failureChar' => '-',     // Char to indicate failure
    );

    /**
     * The ezcConsoleOutput object to use.
     *
     * @var ezcConsoleOutput
     */
    protected $outputHandler;

    /**
     * Counter for success and failure outputs. 
     * 
     * @var array
     */
    protected $counter = array( 
        true  => 0,
        false => 0,
    );

    /**
     * Creates a new status bar.
     *
     * @param ezcConsoleOutput $outHandler Handler to utilize for output
     * @param array(string) $settings      Settings
     * @param array(string) $options       Options
     *
     * @see ezcConsoleStatusbar::$options
     */
    public function __construct( ezcConsoleOutput $outHandler, $successChar = '+', $failureChar = '-' )
    {
        $this->outputHandler = $outHandler;
        $this->__set( 'successChar', $successChar );
        $this->__set( 'failureChar', $failureChar );
    }

    /**
     * Property read access.
     * 
     * @param string $key Name of the property.
     * @return mixed Value of the property or null.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If the the desired property is not found.
     * @return mixed The value of the desired property.
     */
    public function __get( $key )
    {
        if ( isset( $this->options[$key] ) )
        {
            return $this->options[$key];
        }
        throw new ezcBasePropertyNotFoundException( $key );
    }

    /**
     * Property write access.
     * 
     * @param string $key Name of the property.
     * @param mixed $val  The value for the property.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If a desired property could not be found.
     * @throws ezcBasePropertyException
     *         If a desired property value is out of range.
     * @return void
     */
    public function __set( $key, $val )
    {
        switch ( $key )
        {
            case 'successChar':
            case 'failureChar':
                if ( strlen( $val ) < 1 )
                {
                    throw new ezcBasePropertyException( 
                        $key, 
                        $val,
                        'length > 1'
                    );
                }
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $key );
        }
        $this->options[$key] = $val;
    }
 
    /**
     * Property isset access.
     * 
     * @param string $key Name of the property.
     * @return bool True is the property is set, otherwise false.
     */
    public function __isset( $key )
    {
        return isset( $this->options[$key] );
    }

    /**
     * Add a status to the status bar.
     * Adds a new status to the bar which is printed immediatelly. If the
     * cursor is currently not at the beginning of a line, it will move to
     * the next line.
     *
     * @param bool $status Print successChar on true, failureChar on false.
     * @return void
     */
    public function add( $status )
    {
        switch ( $status )
        {
            case true:
                $this->outputHandler->outputText( $this->successChar, 'success' );
                break;

            case false:
                $this->outputHandler->outputText( $this->failureChar, 'failure' );
                break;
            
            default:
                trigger_error( 'Unknown status '.var_export( $status, true ).'.', E_USER_WARNING );
                return;
        }
        $this->counter[$status]++;
    }

    /**
     * Reset the state of the statusbar object to its initial one. 
     * 
     * @return void
     */
    public function reset()
    {
        foreach ( $this->counter as $status => $count )
        {
            $this->counter[$status] = 0;
        }
    }

    /**
     * Returns number of successes during the run.
     * Returns the number of success characters printed from this status bar.
     * 
     * @return int Number of successes.
     */
    public function getSuccesses()
    {
        return $this->counter[true];
    }

    /**
     * Returns number of failures during the run.
     * Returns the number of failure characters printed from this status bar.
     * 
     * @return int Number of failures.
     */
    public function getFailures()
    {
        return $this->counter[false];
    }
}
?>
