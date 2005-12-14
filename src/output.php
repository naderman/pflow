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
 *
 * The ezcConsoleOutput class provides an interface to output text to the console. It deals with formating 
 * text in different ways and offers some comfortable options to deal
 * with console text output.
 *
 * <code>
 *
 * // Create the output handler
 * $out = new ezcConsoleOutput();
 * 
 * // Set the verbosity to level 10
 * $out->options->verboseLevel = 10;
 * // Enable auto wrapping of lines after 40 characters
 * $out->options->autobreak    = 40;
 * 
 * // Set the color of the default output format to green
 * $out->formats->default->color   = 'green';
 *
 * // Set the color of the output format named 'success' to white
 * $out->formats->success->color   = 'white';
 * // Set the style of the output format named 'success' to bold
 * $out->formats->success->style   = array( 'bold' );
 *
 * // Set the color of the output format named 'failure' to red
 * $out->formats->failure->color   = 'red';
 * // Set the style of the output format named 'failure' to bold
 * $out->formats->failure->style   = array( 'bold' );
 * // Set the background color of the output format named 'failure' to blue
 * $out->formats->failure->bgcolor = 'blue';
 *
 * // Output text with default format
 * $out->outputText( 'This is default text ' );
 * // Output text with format 'success'
 * $out->outputText( 'including success message', 'success' );
 * // Some more output with default output.
 * $out->outputText( "and a manual linebreak.\n" );
 *
 * // Manipulate the later output
 * $out->formats->success->color = 'green';
 * $out->formats->default->color = 'blue';
 *
 * // This is visible, since we set verboseLevel to 10, and printed in default format (now blue)
 * $out->outputText( "Some verbose output.\n", null, 10 );
 * // This is visible, since we set verboseLevel to 10, and printed in format 'failure'
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
     * @var object(ezcConsoleOutputOptions)
     */
    protected $options;

    /**
     * Formats 
     * 
     * @var object(ezcConsoleOutputFormats)
     */
    protected $formats;

    /**
     * Whether a position has been stored before, using the
     * storePos() method.
     *
     * @see ezcConsoleOutput::storePos()
     * @var bool
     */
    protected $positionStored = false;

    /**
     * Stores the mapping of color names to their escape
     * sequence values.
     *
     * @var array(string => int)
     */
    protected static $color = array(
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
     * @var array(string => int)
     */
    protected static $bgcolor = array(
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
    protected static $style = array( 
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
     * Create a new console output handler.
     *
     * @see ezcConsoleOutput::$options
     * @see ezcConsoleOutputOptions
     * @see ezcConsoleOutput::$formats
     * @see ezcConsoleOutputFormats
     *
     * @param ezcConsoleOutputOptions $options Options.
     * @param ezcConsoleOutputFormats $formats Formats.
     */
    public function __construct( ezcConsoleOutputOptions $options = null, ezcConsoleOutputFormats $formats = null )
    {
        $options = isset( $options ) ? $options : new ezcConsoleOutputOptions();
        $formats = isset( $formats ) ? $formats : new ezcConsoleOutputFormats();
        $this->options = $options;
        $this->formats = $formats;
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
        switch ($key) 
        {
            case 'options':
                return $this->options;
                break;
            case 'formats':
                return $this->formats;
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
     * @throws ezcBaseConfigException
     *         If a the value for the property options is not an instance of
     *         ezcConsoleOutputOptions
     *         {@link ezcBaseConfigException::VALUE_OUT_OF_RANGE}.
     */
    public function __set( $key, $val )
    {
        switch ($key) 
        {
            case 'options':
                if ( !( $val instanceof ezcConsoleOutputOptions ) )
                {
                    throw new ezcBaseConfigException( 
                        'options',
                        ezcBaseConfigException::VALUE_OUT_OF_RANGE,
                        is_object( $val ) ? get_class( $val ) : gettype( $val )
                    );
                }
                $this->options = $val;
                return;
                break;
            case 'formats':
                if ( !( $val instanceof ezcConsoleOutputFormats ) )
                {
                    throw new ezcBaseConfigException( 
                        'formats',
                        ezcBaseConfigException::VALUE_OUT_OF_RANGE,
                        is_object( $val ) ? get_class( $val ) : gettype( $val )
                    );
                }
                $this->formats = $val;
                return;
                break;
            default:
                break;
        }
        throw new ezcBasePropertyNotFoundException( $key );
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
        if ( $this->options->verboseLevel >= $verboseLevel ) 
        {
            if ( $this->options->autobreak > 0 )
            {
                $textLines = explode( "\n", $text );
                foreach ( $textLines as $id => $textLine )
                {
                    $textLines[$id] = wordwrap( $textLine, $this->options->autobreak, "\n", true );
                }
                $text = implode( "\n", $textLines );
            }
            echo ( $this->options->useFormats == true ) ? $this->styleText( $text, $format ) : $text;
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
     * Returns if a format code is valid for ta specific formating option.
     * This method determines, if a given code is valid for a specific formating
     * option ('color', 'bgcolor' or 'style').
     * 
     * @see ezcConsoleOutput::getFormatCode();
     *
     * @param string $type Formating type.
     * @param string $key  Format option name.
     * @return bool True if the code is valid.
     */
    public static function isValidFormatCode( $type, $key )
    {
        return isset( self::${$type}[$key] );
    }

    /**
     * Returns the escape sequence for a specific format.
     * Returns the default format escape sequence, if the requested format does 
     * not exist.
     * 
     * @param string $format Name of the format.
     * @return string The escpe sequence.
     */
    protected function buildSequence( $format = 'default' )
    {
        if ( $format === 'default' )
        {
            return sprintf( $this->escapeSequence, 0 );
        }
        $modifiers = array();
        $formats = array( 'color', 'style', 'bgcolor' );
        foreach ( $formats as $formatType ) 
        {
            // Get modifiers
            if ( is_array( $this->formats->$format->$formatType ) )
            {
                if ( !in_array( 'default', $this->formats->$format->$formatType ) )
                {
                    foreach ( $this->formats->$format->$formatType as $singleVal ) 
                    {
                        $modifiers[] = $this->getFormatCode( $formatType, $singleVal );
                    }
                }
            }
            else
            {
                if ( $this->formats->$format->$formatType !== 'default' )
                {
                    $modifiers[] = $this->getFormatCode( $formatType, $this->formats->$format->$formatType );
                }
            }
        }
        // Merge modifiers
        return sprintf( $this->escapeSequence, implode( ';', $modifiers ) );
    }

    /**
     * Returns the code for a given formating option of a given type.
     * $type is the type of formating ('color', 'bgcolor' or 'style'),
     * $key the name of the format to lookup. Returns the numeric code for
     * the requested format or 0 if format or type do not exist.
     * 
     * @see ezcConsoleOutput::isValidFormatCode()
     * 
     * @param string $type Formating type.
     * @param string $key  Format option name.
     * @return int The code representation.
     */
    protected function getFormatCode( $type, $key )
    {
        if ( !ezcConsoleOutput::isValidFormatCode( $type, $key ) ) 
        {
            return 0;
        }
        return ezcConsoleOutput::${$type}[$key];
    }

}
?>
