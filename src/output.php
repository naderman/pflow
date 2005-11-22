<?php
/**
 * File containing the ezcConsoleOutput class.
 *
 * @package ConsoleTools
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
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
 *  'format'       => array(
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
 * $out = new ezcConsoleOutput( $opts );
 *
 * $out->outputText( 'This is default text ' );
 * $out->outputText( 'including success message', 'success' );
 * $out->outputText( "and a manual linebreak.\n" );
 *
 * $out->outputText( "Some verbose output.\n", null, 10 );
 * $out->outputText( "And some not so verbose, failure output.\n", 'failure', 5 );
 *
 * </code>
 * 
 * @package ConsoleTools
 * @version //autogen//
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
     *   'format'       => array(
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
        'autobreak'     => 0,
        'useFormats'    => true,    // Whether to enable formatting or not
        'format'        => array(
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
		'gray'          => 30,
		'red'           => 31,
		'green'         => 32,
		'yellow'        => 33,
		'blue'          => 34,
		'magenta'       => 35,
		'cyan'          => 36,
		'white'         => 37,
        'default'       => 39
    );

    /**
     * Stores the mapping of bgcolor names to their escape
     * sequence values.
     * 
     * @var array
     */
    private $bgcolors = array(
        'black'      => 40,
        'red'        => 41,
		'green'      => 42,
		'yellow'     => 43,
		'blue'       => 44,
		'magenta'    => 45,
		'cyan'       => 46,
		'white'      => 47,
        'default'    => 49,
    );

    /**
     * Stores the mapping of styles names to their escape
     * sequence values.
     * 
     * @var array(string => int)
     */
    private $styles = array( 
        'default'           => '0',
    
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
     * Basic escape sequence string. Use sprintf() to insert escape codes.
     * 
     * @var string
     */
    private $escapeSequence = "\033[%sm";

    /**
     * Hard coded default format definition (fallback solution).
     * 
     * @var array
     */
    private $defaultFormat = array( 
        'color'     => 'default',
        'bgcolor'   => 'default',
        'style'     => 'default',
    );

    /**
     * Whether a position has been stored before, using the
     * storePos() method.
     *
     * @see ezcConsoleOutput::storePos()
     * @var bool
     */
    protected $positionStored = false;

    /**
     * Create a new console output handler.
     *
     * @see ezcConsoleOutput::$options
     * @see ezcConsoleOutput::setOptions()
     * @see ezcConsoleOutput::getOptions()
     *
     * @param array(string) $options Options.
     */
    public function __construct( $options = array() )
    {
        $this->setOptions( $options );
        
    }

    /**
     * Set options.
     *
     * @see ezcConsoleOutput::getOptions()
     * @see ezcConsoleOutput::$options
     *
     * @param array(string) $options Options.
     */
    public function setOptions( $options )
    {
        if ( isset( $options['format'] ) ) 
        {
            $this->setFormats( $options['format'] );
            unset( $options['format'] );
        }
        foreach ( $options as $name => $val ) 
        {
            if ( isset( $this->options[$name] ) ) 
            {
                $this->options[$name] = $val;
            } 
            else 
            {
                trigger_error( "Unknowen option <{$name}>.", E_USER_WARNING );
            }
        }
    }

    /**
     * Returns options
     *
     * @see ezcConsoleOutput::setOptions()
     * @see ezcConsoleOutput::$options
     * 
     * @return array(string) Options.
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns an array of the formats registered. 
     * 
     * @return array(string) The defined formats.
     */
    public function getFormats()
    {
        return $this->options['format'];
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
    public function outputText( $text, $format = 'default', $verboseLevel = 1 ) 
    {
        if ( $this->options['verboseLevel'] >= $verboseLevel ) 
        {
            if ( $this->options['autobreak'] > 0 )
            {
                $textLines = explode( "\n", $text );
                foreach ( $textLines as $id => $textLine )
                {
                    $textLines[$id] = wordwrap( $textLine, $this->options['autobreak'], "\n", true );
                }
                $text = implode( "\n", $textLines );
            }
            echo ( $this->options['useFormats'] == true ) ? $this->styleText( $text, $format ) : $text;
        }
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
    public function styleText( $text, $format = 'default' ) 
    {
        return $this->buildSequence( $format ) . $text . $this->buildSequence( 'default' );
    }

    /**
     * Store the current cursor position.
     * Saves the current cursor position to return to it using 
     * {@link ezcConsoleOutput::restorePos()}. Multiple calls
     * to this method will override each other. Only the last
     * position is saved.
     *
     */
    public function storePos() 
    {
        echo "\033[s";
        $this->positionStored = true;
    }

    /**
     * Restore a cursor position.
     * Restores the cursor position last saved using
     * {@link ezcConsoleOutput::storePos()}.
     *
     *
     * @throws ezcConsoleOutputException If no position saved.
     * @todo Gnome terminal does not recognize this codes. Solution??
     */
    public function restorePos() 
    {
        if ( $this->positionStored === false )
        {
            throw new ezcConsoleOutputException( 'Cannot restore position, if no position has been stored before.',  ezcConsoleOutputException::NO_POSITION_STORED );
        }
        echo "\033[u";
    }

    /**
     * Move the cursor to a specific column of the current line.
     * Moves the cursor to a specific column index of the current line (
     * default is 1).
     * 
     * @param int $col Column to jump to.
     */
    public function toPos( $col = 1 ) 
    {
        echo "\033[{$column}G";
    }

    /**
     * Set formating options.
     * Sub method to set formating options.
     * 
     * @param array $newFormats Array of new formats to be set.
     */
    private function setFormats( $newFormats )
    {
        foreach ( $newFormats as $name => $format ) 
        {
            $this->options['format'][$name] = $format;
        }
    }

    /**
     * Returns the escape sequence for a specific format.
     * Returns the default format escape sequence, if the requested format does 
     * not exist.
     * 
     * @param string $format Name of the format.
     * @return string The escpe sequence.
     */
    private function buildSequence( $format = 'default' )
    {
        if ( $format === 'default' || !isset( $this->options['format'][$format] ) )
        {
            return sprintf( $this->escapeSequence, 0 );
        }
        $modifiers = array();
        $format = $this->options['format'][$format];
        foreach ( $format as $formatType => $value ) 
        {
            // Get modifiers
            if ( is_array( $value ) )
            {
                foreach ( $value as $singleVal ) 
                {
                    $modifiers[] = $this->getFormatCode( $formatType, $singleVal );
                }
            }
            else
            {
                $modifiers[] = $this->getFormatCode( $formatType, $value );
            }
        }
        // Merge modifiers
        return sprintf( $this->escapeSequence, implode( ';', $modifiers ) );
    }

    /**
     * Returns the code for a given formating option of a given type.
     * $type is the type of formatting ( 'color', 'bgcolor' or 'style' ),
     * $key the name of the format to lookup. Returns the numeric code for
     * the requested format or 0 if format or type do not exist.
     * 
     * @param string $type Formating type.
     * @param string $key  Format option name.
     * @return int The code representation.
     */
    private function getFormatCode( $type, $key )
    {
        $attrName = $type . 's';
        if ( !isset( $this->$attrName ) || !isset( $this->{$attrName}[$key] ) ) 
        {
            return 0;
        }
        return $this->{$attrName}[$key];
    }
}
?>
