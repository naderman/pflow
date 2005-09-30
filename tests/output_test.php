<?php

/**
 * ezcConsoleToolsOutputTest 
 * 
 * @package ConsoleTools
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 */
class ezcConsoleToolsOutputTest extends ezcTestCase
{

    // {{{ $testString

    /**
     * testString 
     * 
     * @var string
     */
    private $testString = "a passion for php";

    // }}}
    // {{{ $testFormats

    private $testFormats = array(
        'color_only_1' => array(
            'in'  => array( 
                'color' => 'blue',
            ),
            'out' => "\033[34;49;22;23;24;27m%s\033[39;49;22;23;24;27m"
        ),
        'color_only_2' => array( 
            'in'  => array( 
                'color' => 'red',
            ),
            'out' => "\033[31;49;22;23;24;27m%s\033[39;49;22;23;24;27m"
        ),
        'bgcolor_only_1' => array( 
            'in'  => array( 
                'bgcolor' => 'green',
            ),
            'out' => "\033[39;42;22;23;24;27m%s\033[39;49;22;23;24;27m"
        ),
        'bgcolor_only_2' => array( 
            'in'  => array( 
                'bgcolor' => 'yellow',
            ),
            'out' => "\033[39;43;22;23;24;27m%s\033[39;49;22;23;24;27m"
        ),
        'style_only_1' => array( 
            'in'  => array( 
                'style' => 'bold',
            ),
            'out' => "\033[39;49;1m%s\033[39;49;22;23;24;27m"
        ),
        'style_only_2' => array( 
            'in'  => array( 
                'style' => 'negative',
            ),
            'out' => "\033[39;49;7m%s\033[39;49;22;23;24;27m"
        ),
    );

    // }}}
    // {{{ $testOptions

    private $testOptions = array( 
        'set_1' => array( 
            'verboseLevel'      => 1,
        ),
        'set_2' => array( 
            'verboseLevel'      => 5,
            'autobreak'         => 20,
            'useFormats'        => false,
        ),
        'set_3' => array( 
            'autobreak'         => 5,
            'useFormats'        => true,
            'format'            => array( 
                'color' => 'blue',
                'bgcolor' => 'green',
                'style' => 'negative',
            ),
        ),
    );

    // }}}

    // {{{ $consoleOutput

    /**
     * consoleOutput 
     * 
     * @var mixed
     */
    private $consoleOutput;

    // }}}
    
    // {{{   suite()

	public static function suite()
	{
		return new ezcTestSuite( "ezcConsoleToolsOutputTest" );
	}

    // }}}

    // {{{ setUp() 

    /**
     * setUp 
     * 
     * @access public
     * @return 
     */
    public function setUp()
    {
        $options = array();
        foreach ( $this->testFormats as $name => $inout ) 
        {
            $options['format'][$name] = $inout['in'];
        }
        $this->consoleOutput = new ezcConsoleOutput( $options );
    }

    // }}} 
    // {{{ tearDown()  

    /**
     * tearDown 
     * 
     * @access public
     * @return 
     */
    public function tearDown()
    {
        unset( $this->consoleOutput );
    }

    // }}} 

    // {{{ testSetOptions()

    /**
     * testSetOptions
     * 
     * @access public
     * @return 
     */
    public function testSetOptions()
    {
        foreach ( $this->testOptions as $name => $optIn )
        {
            $this->consoleOutput->setOptions( $optIn );
            $optOut = $this->consoleOutput->getOptions();
            $this->assertTrue( array_intersect( $optIn, $optOut ) == $optIn, 'Options not correctly set. Returned options array did not contain options set before.' );
        }
    }

    // }}}
    // {{{ testFormatText()

    /**
     * testFormatText
     * 
     * @access public
     * @return 
     */
    public function testFormatText()
    {
        foreach ( $this->testFormats as $name => $inout ) 
        {
            $realRes = $this->consoleOutput->styleText( $this->testString, $name );
            $fakeRes = sprintf( $inout['out'], $this->testString );
            $this->assertTrue( $realRes == $fakeRes, 'Test "' . $name . ' faile. String "' . $realRes . '" (real) is not equal to "' . $fakeRes . '" (fake).' );
        }
    }

    // }}}
    // {{{ testOutputText()

    /**
     * testOutputText
     * 
     * @access public
     * @return 
     */
    public function testOutputText()
    {
        foreach ( $this->testFormats as $name => $inout ) 
        {
            ob_start();
            $this->consoleOutput->outputText( $this->testString, $name );
            $realRes = ob_get_contents();
            ob_clean();
            $fakeRes = sprintf( $inout['out'], $this->testString );
            $this->assertTrue( $realRes == $fakeRes, 'Test "' . $name . ' faile. String "' . $realRes . '" (real) is not equal to "' . $fakeRes . '" (fake).' );
        }
    }

    // }}}

    // {{{ dumpString()

    /**
     * dumpString 
     * 
     * @param mixed $string 
     * @return void
     */
    private function dumpString( $string )
    {
        echo "Dumping string of length ".strlen( $string ).":\n\n";
        for ( $i = 0; $i < strlen( $string ); $i++ )
        {
            echo '"' . $string[$i] . '" = -' . ord( $string[$i] ) . "-\n";
        }
        echo "Finished dumping string.\n\n";
    }

    // }}}
}

?>
