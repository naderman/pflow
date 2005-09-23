<?php
/**
 * File containing the ezcConsoleOutput class.
 *
 * @package ConsoleTools
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 */

/**
 * Class for handling console output.
 * This class handles outputting text to the console. It deals with formating 
 * text in different ways and offers some comfortable options to deal
 * with console text output.
 *
 * <code>
 *
 * $opts = array(
 *  'verboseLevel'  => 10,  // extremly verbose
 *  'autobreak'     => 40,  // will break lines every 40 chars
 *  'formats'       => array(
 *      'default'   => array(     // pseudo format (used when no format given)
 *          'color'   => 'green', // green  foreground color
 *      ),
 *      'success'   => array(     // define format "success"
 *          'color'   => 'white', // white foreground color
 *          'style'   => 'bold',  // bold font style
 *      ),
 *      'failure'   => array(     // Define format "failur"
 *          'color'   => 'black', // black foreground color
 *          'bgcolor' => 'red',   // red background color
 *          'style'   => 'bold',  // bold font style
 *      ),
 *  ),
 * );
 * $out = new ezcConsoleOutput($opts);
 *
 * $out->outputText('This is default text ');
 * $out->outputText('including success message', 'success');
 * $out->outputText("and a manual linebreak.\n");
 *
 * $out->outputText("Some verbose output.\n", null, 10);
 * $out->outputText("And some not so verbose, failure output.\n", 'failure', 5);
 *
 * </code>
 * 
 * @package ConsoleTools
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 */
class ezcConsoleOutput
{
    /**
     * Options
     *
     * Default values:
     * <code>
     * array(
     *   'verboseLevel'  => 1,       // Verbosity level
     *   'autobreak'     => 0,       // Pos <int>. Break lines automatically
     *                               // after this ammount of chars
     *   'useFormats'    => true,    // Whether to enable formatting or not
     *   'formats'       => array(
     *      'default'    => array(     // pseudo format (used when no format given)
     *          'color'   => 'green', // green  foreground color
     *      ),
     *      'success'    => array(     // define format "success"
     *          'color'   => 'white', // white foreground color
     *          'style'   => 'bold',  // bold font style
     *      ),
     *      'failure'    => array(    // Define format "failure"
     *          'color'   => 'black', // black foreground color
     *          'bgcolor' => 'red',   // red background color
     *          'style'   => array(   // multiple styles
     *              'bold',           // bold font style
     *              'blink'           // blinking font
     *           ),
     *      ),
     *  ),
     * );
     * </code>
     *
     * @see ezcConsoleOutput::setOptions()
     * @see ezcConsoleOutput::getOptions()
     * 
     * @var array(string)
     */
    private $options = array(
        'verboseLevel'  => 1,
        'useFormats'    => true,    // Whether to enable formatting or not
        'formats'       => array(
            // default format not re-defined by default (uses system default)
            'success'    => array(     // define format "success" (standard format)
                'color'   => 'green',  // green foreground color
                'style'   => 'bold',   // bold font style
            ),
            'failure'    => array(     // define format "failure" (standard format)
                'color'   => 'red',    // red foreground color
                'style'   => array(    // multiple styles
                    'bold',     // bold font style
                    'blink'     // blinking font
                ),
            ),
        ),
    );

    /**
     * Stores the mapping of color names to their escape
     * sequence values.
     *
     * @var array(string => int)
     */
    private $colors = array(
		'red'           => 31,
		'green'         => 32,
		'yellow'        => 33,
		'blue'          => 34,
		'magenta'       => 35,
		'cyan'          => 36,
		'white'         => 37,
		'gray'          => 30,
    );
	
    /**
     * Stores the mapping of bgcolor names to their escape
     * sequence values.
     * 
     * @var array
     */
    private $bgcolors = array(
        'red'        => 41,
		'green'      => 42,
		'yellow'     => 43,
		'blue'       => 44,
		'magenta'    => 45,
		'cyan'       => 46,
		'white'      => 47,
    );

    /**
     * Stores the mapping of styles names to their escape
     * sequence values.
     * 
     * @var array(string => int)
     */
    private $styles = array( 
        'bold'              => 1,
        'faint'             => 2,
        'normal'            => 22,
        
        'italic'            => 3,
        'notitalic'         => 23,
        
        'underlined'        => 4,
        'doubleunderlined'  => 21,
        'notunderlined'     => 24,
        
        'blink'             => 5,
        'blinkfast'         => 6,
        'noblink'           => 25,
        
        'negative'          => 7,
        'positive'          => 27,
    );

    /**
     * Create a new console output handler.
     *
     * @see ezcConsoleOutput::$options
     * @see ezcConsoleOutput::setOptions()
     * @see ezcConsoleOutput::getOptions()
     *
     * @param array(string) $options Options.
     */
    public function __construct( $options = array() ) {
        $this->setOptions( $options );
        
    }

    /**
     * Set options.
     *
     * @see ezcConsoleOutput::getOptions()
     * @see ezcConsoleOutput::$options
     *
     * @param array(string) $options Options.
     * @return void
     */
    public function setOptions( $options ) {
        $this->setOptionsRecursive( $this->options, $options );
    }

    /**
     * Returns options
     *
     * @see ezcConsoleOutput::setOptions()
     * @see ezcConsoleOutput::$options
     * 
     * @return array(string) Options.
     */
    public function getOptions( ) {
        return $this->options;
    }

    /**
     * Print text to the console.
     * Output a string to the console. If $format parameter is ommited, 
     * the default style is chosen. Style can either be a special style
     * {@link eczConsoleOutput::$options}, a style name 
     * {@link ezcConsoleOutput$formats} or 'none' to print without any styling.
     *
     * @param string $text       The text to print.
     * @param string $format     Format chosen for printing.
     * @param int $verboseLevel On which verbose level to output this message.
     * @param int Output this text only in a specific verbosity level
     */
    public function outputText( $text, $format = 'default', $verboseLevel = 1 ) {
        
    }
    
    /**
     * Returns a styled version of the text.
     * Receive a styled version of the inputed text. If $format parameter is 
     * ommited, the default style is chosen. Style can either be a special 
     * style or a direct color name.
     * 
     * {@link ezcConsoleOutput::$options}, a style name 
     * {@link ezcConsoleOutput::$formats} or 'none' to print without any styling.
     *
     * @param string $text   Text to apply style to.
     * @param string $format Format chosen to be applied.
     * @return string
     */
    public function styleText( $text, $format = 'default' ) {
        $format = $this->getStyle;
    }

    /**
     * Store the current cursor position.
     * Saves the current cursor position to return to it using 
     * {@link ezcConsoleOutput::restorePos()}. Multiple calls
     * to this method will override each other. Only the last
     * position is saved.
     *
     * @todo Shall multiple markers be supported? Must be emulated by the 
     *       class, since not directly supported by ANSI escape seqs.
     *
     * @return void
     */
    public function storePos( ) {
        
    }

    /**
     * Restore a cursor position.
     * Restores the cursor position last saved using
     * {@link ezcConsoleOutput::storePos()}.
     *
     * @return void
     *
     * @throws ezcConsoleOutputException If no position saved.
     */
    public function restorePos( ) {
        
    }

    /**
     * Set options recursivly without loosing options already set.
     * This methed recursivly sets the options from an array submitted
     * to it. Existing options which are not set to be changed will
     * be protected.
     * 
     * @param array $current Reference to the current array to process.
     * @param array $new     (Multidimensional) Array of new options to be set.
     * @return void
     */
    private function setOptionsRecursive( &$current, $new )
    {
        foreach ( $new as $key => $val ) {
            if ( !isset( $current[$key] ) ) {
                trigger_error('Unknowen option "' . $key  . '".', E_USER_WARNING);
                continue;
            }
            if ( is_array( $new[$key] ) ) {
                $this->setOptionsRecursive( $current[$key], $new[$key] );
            } else {
                $current[$key] = $new[$key];
            }
        }
    }
}
