<?php
/**
 * File containing the ezcConsoleProgressbar class.
 *
 * @package ConsoleTools
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Creating and maintaining progressbars to be printed to the console. 
 *
 * <code>
 *
 * $out = new ezcConsoleOutput();
 * 
 * $opt = array(
 *  'emptyChar'     => '-',
 *  'progressChar'  => '#',
 *  'formatString'  => 'Uploading file '.$myFilename.' %act%/%max% kb [%bar%] %fraction%%',
 * );
 * $progress = new ezcConsoleProgressbar( $out, 150, 5 );
 *
 * while ( $file->upload() ) {
 *      $progress->advance();
 * }
 * $progress->finish();
 * $out->outputText( "Successfully uploaded $myFilename.\n", 'success' );
 *
 * </code>
 *  
 * 
 * @package ConsoleTools
 * @version //autogen//
 */
class ezcConsoleProgressbar
{
    /**
     * Settings for the progress bar.
     * Contains settings for the progress bar. Mandatory setting values are:
     *
     * <code>
     * $progress->max;      // The maximum progress value to reach.
     * $ptogress->step;     // The step size to raise the progress.
     * </code>
     * 
     * @var array
     */
    protected $settings;

    /**
     * Options
     *
     * @var object(ezcConsoleProgressbarOptions)
     */
    protected $options;

    /**
     * Storage for actual values to be replaced in the format string.
     * Actual values are stored here and will be inserted into the bar
     * before printing it.
     * 
     * @var array(string => string)
     */
    protected $valueMap = array( 
        'bar'       => '',
        'fraction'  => '',
        'act'       => '',
        'max'       => '',
    );

    /**
     * One tima calculated measures.
     * This array saves how much space a specific part of the bar utilizes to not
     * recalculate those on every step.
     * 
     * @var array(string => int)
     */
    protected $measures = array( 
        'barSpace'          => 0,
        'fractionSpace'     => 0,
        'actSpace'          => 0,
        'maxSpace'          => 0,
        'fixedCharSpace'    => 0,
    );

    /**
     * The current step the progress bar should show. 
     * 
     * @var int
     */
    protected $currentStep = 0;

    /**
     * The maximum number of steps to go.
     * Calculated once from the settings.
     *
     * @var int
     */
    protected $numSteps = 0;

    /**
     * The ezcConsoleOutput object to use.
     *
     * @var ezcConsoleOutput
     */
    protected $output;

    /**
     * Indicates if the starting point for the bar has been stored.
     * Per default this is false to indicate that no start position has been
     * stored, yet.
     * 
     * @var bool
     */
    protected $started = false;

    /**
     * Creates a new progress bar.
     *
     * @param ezcConsoleOutput $outHandler Handler to utilize for output
     * @param array(string) $settings      Settings
     * @param array(string) $options       Options
     *
     * @see ezcConsoleTable::$settings
     * @see ezcConsoleTable::$options
     */
    public function __construct( ezcConsoleOutput $outHandler, $max, $step, ezcConsoleProgressbarOptions $options = null )
    {
        $this->output = $outHandler;
        $this->__set( 'max', $max );
        $this->__set( 'step', $step );
        $this->__set( 'options', isset( $options ) ? $options : new ezcConsoleProgressbarOptions() );
    }

    /**
     * Property read access.
     * 
     * @param string $key Name of the property.
     * @return mixed Value of the property or null.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If the the desired property is not found.
     */
    public function __get( $key )
    {
        switch ( $key )
        {
            case 'options':
                return $this->options;
                break;
            case 'max':
            case 'step':
                return $this->settings[$key];
                break;
            default:
                break;
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
     * @throws ezcBaseConfigException
     *         If a desired property value is out of range
     *         {@link ezcBaseConfigException::VALUE_OUT_OF_RANGE}.
     */
    public function __set( $key, $val )
    {
        switch ( $key )
        {
            case 'options':
                if ( !( $val instanceof ezcConsoleProgressbarOptions ) )
                {
                    throw new ezcBaseTypeException( 'ezcConsoleProgressbarOptions', gettype( $val ) );
                };
                break;
            case 'max':
            case 'step':
                if ( !is_int( $val ) || $val < 0 )
                {
                    throw new ezcBaseConfigException( $key, ezcBaseConfigException::VALUE_OUT_OF_RANGE, $val );
                }
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $key );
                break;
        }
        // Changes settings or options, need for recalculating measures
        $this->started = false;
        $this->$key = $val;
    }
 
    /**
     * Property isset access.
     * 
     * @param string $key Name of the property.
     * @return bool True is the property is set, otherwise false.
     */
    public function __isset( $key )
    {
        switch ( $key )
        {
            case 'options':
            case 'max':
            case 'step':
                return true;
                break;
            default:
        }
        return false;
    }

    /**
     * Start the progress bar
     * Starts the progess bar and sticks it to the current line.
     * No output will be done yet. Call {@link ezcConsoleProgressbar::output()}
     * to print the bar.
     * 
     */
    public function start() 
    {
        $this->calculateMeasures();
        $this->output->storePos();
        $this->started = true;
    }

    /**
     * Draw the progress bar.
     * Prints the progressbar to the screen. If start() has not been called 
     * yet, the current line is used for {@link ezcConsolProgressbar::start()}.
     */
    public function output()
    {
        if ( $this->started === false )
        {
            $this->start();
        }
        $this->output->restorePos();
        $this->generateValues();
        echo $this->insertValues();
    }

    /**
     * Advance the progress bar.
     * Advances the progress bar by one step. Redraws the bar by default, using
     * the {@link ezcConsoleProgressbar::output()} method.
     *
     * @param bool Whether to redraw the bar immediatelly.
     */
    public function advance( $redraw = true ) 
    {
        $this->currentStep += 1;
        if ( $redraw === true )
        {
            $this->output();
        }
    }

    /**
     * Finish the progress bar.
     * Finishes the bar (jump to 100% if not happened yet,...) and jumps
     * to the next line to allow new output. Also resets the values of the
     * output handler used, if changed.
     */
    public function finish()
    {
        $this->currentStep = $this->numSteps;
        $this->output();
    }

    /**
     * Generate all values to be replaced in the format string. 
     * 
     */
    protected function generateValues()
    {
        // Bar
        $barFilledSpace = ceil( $this->measures['barSpace'] / $this->numSteps ) * $this->currentStep;
        // Sanitize value if it gets to large by rounding
        $barFilledSpace = $barFilledSpace > $this->measures['barSpace'] ? $this->measures['barSpace'] : $barFilledSpace;
        $bar = str_pad( 
            str_pad( 
                $this->options->progressChar, 
                $barFilledSpace, 
                $this->options->barChar, 
                STR_PAD_LEFT
            ), 
            $this->measures['barSpace'], 
            $this->options->emptyChar, 
            STR_PAD_RIGHT 
        );
        $this->valueMap['bar'] = $bar;

        // Fraction
        $fractionVal = sprintf( 
            $this->options->fractionFormat,
            ( $fractionVal = round( ( $this->step * $this->currentStep ) / $this->max * 100 ) ) > 100 ? 100 : $fractionVal
        );
        $this->valueMap['fraction'] = str_pad( 
            $fractionVal, 
            strlen( sprintf( $this->options->fractionFormat, 100 ) ),
            ' ',
            STR_PAD_LEFT
        );

        // Act / max
        $actVal = ( $actVal = $this->currentStep * $this->step ) > $this->max ? $this->max : $actVal;
        $this->valueMap['act'] = str_pad( 
            $actVal, 
            strlen( $this->max ),
            ' ',
            STR_PAD_LEFT
        );
        $this->valueMap['max'] = $this->max;
    }

    /**
     * Insert values into bar format string. 
     * 
     */
    protected function insertValues()
    {
        $bar = $this->options->formatString;
        foreach ( $this->valueMap as $name => $val )
        {
            $bar = str_replace( "%{$name}%", $val, $bar );
        }
        return $bar;
    }

    /**
     * Calculate several measures necessary to generate a bar. 
     * 
     */
    protected function calculateMeasures()
    {
        // Calc number of steps bar goes through
        $this->numSteps = (int)round( $this->max / $this->step );
        // Calculate measures
        $this->measures['fixedCharSpace'] = strlen( $this->stripEscapeSequences( $this->insertValues() ) );
        if ( strpos( $this->options->formatString,'%max%' ) !== false )
        {
            $this->measures['maxSpace'] = strlen( $this->max );

        }
        if ( strpos( $this->options->formatString, '%act%' ) !== false )
        {
            $this->measures['actSpace'] = strlen( $this->max );
        }
        if ( strpos( $this->options->formatString, '%fraction%' ) !== false )
        {
            $this->measures['fractionSpace'] = strlen( sprintf( $this->options->fractionFormat, 100 ) );
        }
        $this->measures['barSpace'] = $this->options->width - array_sum( $this->measures );
    }

    /**
     * Strip all escape sequences from a string to measure it's size correctly. 
     * 
     * @param mixed $str 
     */
    protected function stripEscapeSequences( $str )
    {
        return preg_replace( '/\033\[[0-9a-f;]*m/i', '', $str  );
    }
}
?>
