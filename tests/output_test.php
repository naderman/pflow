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
    // {{{ $testData

    private $testData = array( 
        'format' => array( 
            'color_only_1' => array(
                'in'  => array( 
                    'color' => 'blue',
                ),
                'out' => "\033[34;49;22;23;24m%s\033[39;49;22;23;24;27m"
            ),
            'color_only_2' => array( 
                'in'  => array( 
                    'color' => 'red',
                ),
                'out' => "\033[31;49;22;23;24m%s\033[39;49;22;23;24;27m"
            ),
            'bgcolor_only_1' => array( 
                'in'  => array( 
                    'bgcolor' => 'green',
                ),
                'out' => "\033[39;42;22;23;24m%s\033[39;49;22;23;24;27m"
            ),
            'bgcolor_only_2' => array( 
                'in'  => array( 
                    'bgcolor' => 'yellow',
                ),
                'out' => "\033[39;43;22;23;24m%s\033[39;49;22;23;24;27m"
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
                'out' => "\033[39;49;1m%s\033[39;49;22;23;24;27m"
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
        foreach ( $this->testData['format'] as $name => $inout ) 
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

    // {{{ testFormatTextSuccess()

    /**
     * testFormatTextSuccess 
     * 
     * @access public
     * @return 
     */
    public function testFormatTextSuccess()
    {
        foreach ( $this->testData['format'] as $name => $inout ) 
        {
            $realRes = $this->consoleOutput->styleText( $this->testString, $name );
            var_dump( $realRes );
            $fakeRes = sprintf( $inout['out'], $this->testString );
            var_dump( $fakeRes );
            $this->assertTrue( $realRes == $fakeRes );
        }
    }

    // }}}

}

?>
