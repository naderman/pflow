<?php
/**
 * File containing the ezcConsoleProgressbar class.
 *
 * @package ConsoleTools
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license BSD {@link http://ez.no/licenses/bsd}
 * @filesource
 */

/**
 * Creating and maintaining progressbars to be printed to the console. 
 *
 * <code>
 *
 * // ... creating ezcConsoleOutput object
 * 
 * $set = array( 'max' => 150, 'step' => 5 );
 * $opt = array(
 *  'emptyChar'     => '-',
 *  'progressChar'  => '#',
 *  'formatString'  => 'Uploading file '.$myFilename.' %act%/%max% kb [%bar%] %fraction%%',
 * );
 * $progress = new ezcConsoleProgressbar( $out, $set, $opt );
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
     * 
     * <code>
     * array(
     *  'max'   => <int>    // Value to progress to
     *  'step'  => <int>    // Stepwidth
     * );
     * </code>
     * 
     * @var array(string)
     */
    protected $settings;

    /**
     * Options
     *
     * <code>
     * array(
     *   'barChar'       => '+',     // Char to fill progress bar with
     *   'emptyChar'     => ' ',     // Char for empty space in progress bar
     *   'progressChar'  => '>',     // Progress char of the bar filling
     *   'formatString'  => '[%bar%] %fraction%%',   // == "[+++++>    ] 60%"
     *   'width'         => 10,      // Maximum width of the progressbar
     * );
     * </code>
     *
     * 'formatString' can contain the following placeholders:
     *  '%fraction%' => Actual percent value
     *  '%max%'     => Maximum value
     *  '%act%'     => Actual value
     *  '%bar%'     => The actual progressbar
     *
     * @var array(string)
     */
    protected $options = array(
        'barChar'        => '+',     // Char to fill progress bar with
        'emptyChar'      => '-',     // Char to fill empty space in progress bar with
        'progressChar'   => '>',     // Right most char of the progress bar filling
        'formatString'   => '%act% / %max% [%bar%] %fraction%%',   // Format string
        'width'          => 100,     // Maximum width of the progressbar
        'fractionFormat' => '%01.2f',// sprintf() string for the fraction
    );

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
    protected $numSteps;

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
    public function __construct( ezcConsoleOutput $outHandler, $settings, $options = array() )
    {
        $this->output = $outHandler;
        $this->setSettings( $settings );
        $this->setOptions( $options );
        $this->calculateMeasures();
    }

    /**
     * Set options for the table.
     *
     * @see ezcConsoleTable::$options
     * 
     * @param array $options Options to set.
     */
    public function setOptions( $options )
    {
        foreach ( $options as $name => $val ) 
        {
            if ( isset( $this->options[$name] ) ) 
            {
                if ( $name == 'barChar' || $name == 'progressChar' || $name == 'emptyChar' )
                {
                    // Not possible by now, would need some more effort in padding, etc.
                    $val = $this->stripEscapeSequences( $val );
                }
                $this->options[$name] = $val;
            } 
            else 
            {
                trigger_error( "Unknown option <{$name}>.", E_USER_WARNING );
            }
        }
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
        echo strlen( $this->stripEscapeSequences( $this->insertValues(  ) ) );
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
        $this->actualStep = $this->numSteps;
        $this->output();
    }

    /**
     * Check and set the settings submited to the constructor. 
     * 
     * @param array $settings 
     *
     * @throws ezcBaseConfigException On an invalid setting.
     */
    private function setSettings( $settings )
    {
        if ( !isset( $settings['max'] ) || !is_int( $settings['max'] ) || $settings['max'] < 0 ) 
        {
            throw new ezcBaseConfigException( 'Missing or invalid max setting.' );
        }
        if ( !isset( $settings['step'] ) || !is_int( $settings['step'] ) || $settings['step'] < 0 ) 
        {
            throw new ezcBaseConfigException( 'Missing or invalid step setting.' );
        }
        $this->settings = $settings;
        // Calc number of steps bar goes through
        $this->numSteps = $this->settings['max'] / $this->settings['step'];
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
                $this->options['progressChar'], 
                $barFilledSpace, 
                $this->options['barChar'], 
                STR_PAD_LEFT
            ), 
            $this->measures['barSpace'], 
            $this->options['emptyChar'], 
            STR_PAD_RIGHT 
        );
        $this->valueMap['bar'] = $bar;

        // Fraction
        $fractionVal = sprintf( 
            $this->options['fractionFormat'],
            ( $fractionVal = round( ( $this->settings['step'] * $this->currentStep ) / $this->settings['max'] * 100 ) ) > 100 ? 100 : $fractionVal
        );
        $this->valueMap['fraction'] = str_pad( 
            $fractionVal, 
            strlen( sprintf( $this->options['fractionFormat'], 100 ) ),
            ' ',
            STR_PAD_LEFT
        );

        // Act / max
        $actVal = ( $actVal = $this->currentStep * $this->settings['step'] ) > $this->settings['max'] ? $this->settings['max'] : $actVal;
        $this->valueMap['act'] = str_pad( 
            $actVal, 
            strlen( $this->settings['max'] ),
            ' ',
            STR_PAD_LEFT
        );
        $this->valueMap['max'] = $this->settings['max'];
    }

    /**
     * Insert values into bar format string. 
     * 
     */
    protected function insertValues()
    {
        $bar = $this->options['formatString'];
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
        $this->measures['fixedCharSpace'] = strlen( $this->stripEscapeSequences( $this->insertValues() ) );
        if ( strpos( $this->options['formatString'],'%max%' ) !== false )
        {
            $this->measures['maxSpace'] = strlen( $this->settings['max'] );

        }
        if ( strpos( $this->options['formatString'], '%act%' ) !== false )
        {
            $this->measures['actSpace'] = strlen( $this->settings['max'] );
        }
        if ( strpos( $this->options['formatString'], '%fraction%' ) !== false )
        {
            $this->measures['fractionSpace'] = strlen( sprintf( $this->options['fractionFormat'], 100 ) );
        }
        $this->measures['barSpace'] = $this->options['width'] - array_sum( $this->measures );
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
