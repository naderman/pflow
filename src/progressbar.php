<?php
/**
 * File containing the ezcConsoleProgressbar class.
 *
 * @package ConsoleTools
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 */

/**
 * Creating and maintaining progressbars to be printed to the console. 
 *
 * @todo The author of the PEAR package "Console_ProgressBar" accepted
 *       us to take over his code from that package and improve it for
 *       our needs. {@link http://pear.php.net/package/console_progressbar}
 *
 * <code>
 *
 * // ... creating ezcConsoleOutput object
 * 
 * $set = array('max' => 150, 'step' => 5);
 * $opt = array(
 *  'emptyChar'     => '-',
 *  'progressChar'  => '#',
 *  'formatString'  => 'Uploading file '.$myFilename.' %act%/%max% kb [%bar%] %fraction%%',
 * );
 * $progress = new ezcConsoleProgressbar($out, $set, $opt);
 *
 * while( $file->upload() ) {
 *      $progress->advance();
 * }
 * $progress->finish();
 * $out->outputText("Successfully uploaded $myFilename.\n", 'success');
 *
 * </code>
 *  
 * 
 * @package ConsoleTools
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
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
        'barChar'       => '+',     // Char to fill progress bar with
        'emptyChar'     => ' ',     // Char to fill empty space in progress bar with
        'progressChar'  => '>',     // Right most char of the progress bar filling
        'formatString'  => '[%bar%] %fraction% %',   // Format string
        'width'         => 100,     // Maximum width of the progressbar
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

    // {{{ $output

    /**
     * The ezcConsoleOutput object to use.
     *
     * @var ezcConsoleOutput
     */
    protected $output;

    // }}}

    // {{{ $started

    /**
     * Indicates if the starting point for the bar has been stored.
     * Per default this is false to indicate that no start position has been
     * stored, yet.
     * 
     * @var bool
     */
    protected $started = false;

    // }}}
   
    // {{{ __construct()

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
    public function __construct( ezcConsoleOutput $outHandler, $settings, $options = array() ) {
        $this->output = $outHandler;
        $this->setSettings( $settings );
        $this->setOptions( $options );
    }

    // }}}
    
    // {{{ setOptions()

    /**
     * Set options for the table.
     *
     * @see ezcConsoleTable::$options
     * 
     * @param array $options Options to set.
     * @return void
     */
    public function setOptions( $options )
    {
        foreach ( $options as $name => $val ) 
        {
            if ( isset( $this->options[$name] ) ) 
            {
                $this->options[$name] = $val;
            } 
            else 
            {
                trigger_error( 'Unknowen option "' . $name  . '".', E_USER_WARNING );
            }
        }
    }

    // }}}
    
    // {{{ start()

    /**
     * Start the progress bar
     * Starts the progess bar and sticks it to the current line.
     * No output will be done yet. Call {@link ezcConsoleProgressbar::output()}
     * to print the bar.
     * 
     * @return 
     */
    public function start() 
    {
        $this->output->storePos();
        $this->calculateMeasures();
        $this->started = true;
    }

    // }}}
     
    // {{{ output()

    /**
     * Draw the progress bar.
     * Prints the progressbar to the screen. If start() has not been called 
     * yet, the current line is used for {@link ezcConsolProgressbar::start()}.
     */
    public function output() {
        if ( $this->started === false )
        {
            $this->start();
        }
        $this->output->restorePos();
    }

    // }}}

    // {{{ advance()

    /**
     * Advance the progress bar.
     * Advances the progress bar by one step. Redraws the bar by default, using
     * the {@link ezcConsoleProgressbar::output()} method.
     *
     * @param bool Whether to redraw the bar immediatelly.
     */
    public function advance( $redraw = true ) {
        
    }

    // }}}

    // {{{ finish()

    /**
     * Finish the progress bar.
     * Finishes the bar (jump to 100% if not happened yet,...) and jumps
     * to the next line to allow new output. Also resets the values of the
     * output handler used, if changed.
     */
    public function finish() {
        
    }

    // }}}
    
    // {{{ setSettings()

    /**
     * Check and set the settings submited to the constructor. 
     * 
     * @param array $settings 
     * @return void
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
    }

    // }}}
    // {{{ calculateMeasures()

    /**
     * Calculate several measures necessary to generate a bar. 
     * 
     * @return void
     */
    protected function calculateMeasures()
    {
        $this->measures['fixedCharSpace'] = strlen( $this->stripEscapeSequences( $this->insertValues() ) );
        $this->measures['maxSpace']       = $this->measures['actSpace'] = strlen( $this->stripEscapeSequences( $this->settings['max'] ) );
        $this->measures['fractionSpace']  = 3 // max. "100" == 100 %
        $this->measures['barSpace']       = $this->options['width'] - array_sum( $this->measures );
    }

    // }}}

    // {{{ stripEscapeSequences()

    /**
     * Strip all escape sequences from a string to measure it's size correctly. 
     * 
     * @param mixed $str 
     * @return void
     */
    protected function stripEscapeSequences( $str )
    {
        return preg_replace( '/\033\[[0-9a-f;]*m/i', '', $str  )
    }

    // }}}

}
