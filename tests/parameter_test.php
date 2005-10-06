<?php

/**
 * ezcConsoleToolsOutputTest 
 * 
 * @package ConsoleTools
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 */
class ezcConsoleToolsParameterTest extends ezcTestCase
{

    // { {{ $testParams

    private $testParams = array( 
        array( 
            'short'     => 't',
            'long'      => 'testing',
            'options'   => array(),
        ),
        array( 
            'short'     => 's',
            'long'      => 'subway',
            'options'   => array(),
        ),
        array( 
            'short'     => 'o',
            'long'      => 'original',
            'options'   => array(
                'type'      => ezcConsoleParameter::TYPE_STRING,
            ),
        ),
        array( 
            'short'     => 'b',
            'long'      => 'build',
            'options'   => array(
                'type'      => ezcConsoleParameter::TYPE_INT,
                'default'   => 42,
            ),
        ),
        array( 
            'short'     => 'd',
            'long'      => 'destroy',
            'options'   => array(
                'type'      => ezcConsoleParameter::TYPE_STRING,
                'default'   => 'world',
            ),
        ),
        array( 
            'short'     => 'y',
            'long'      => 'yank',
            'options'   => array(
                'type'          => ezcConsoleParameter::TYPE_STRING,
                'multiple'      => true,
                'short'         => 'Some stupid short text.',
                'long'          => 'Some even more stupid, but somewhat longer long describtion.',
            ),
        ),
        array( 
            'short'     => 'c',
            'long'      => 'console',
            'options'   => array(
                'short'         => 'Some stupid short text.',
                'long'          => 'Some even more stupid, but somewhat longer long describtion.',
                'depends'       => array( 't', 'o', 'b', 'y' ),
            ),
        ),
        array( 
            'short'     => 'n',
            'long'      => 'new',
            'options'   => array(
                'depends'       => array( 't', 'o' ),
                'excludes'      => array( 'b', 'y' ),
                'arguments'     => false,
            ),
        ),
    );

    // }}}
    // {{{ $testAliases

    private $testAliasesSuccess = array( 
        array(
            'short' => 'n',
            'long'  => 'nothing',
            'ref'   => 't',
        ),
        array(
            'short' => 's',
            'long'  => 'something',
            'ref'   => 'o',
        ),
    );

    private $testAliasesFailure = array( 
        array(
            'short' => 'l',
            'long'  => 'lurking',
            'ref'   => 'x',
        ),
        array(
            'short' => 'e',
            'long'  => 'elvis',
            'ref'   => 'z',
        ),
    );

    // }}}
    // {{{ $testArgs

    private $testArgsSuccess = array( 
        array(
            'foo.php',
            '-o',
            '"Test string2"',
            '--build',
            '42',
        ),
        array(
            'foo.php',
            '-b',
            '42',
            '--yank',
            '"a"',
            '--yank',
            '"b"',
            '--yank',
            '"c"',
        ),
        array(
            'foo.php',
            '--yank=a',
            '--yank=b',
            '--yank="c"',
            '-y',
            '1',
            '-y',
            '2'
        ),
        array(
            'foo.php',
            '--yank=a',
            '--yank=b',
            '-y',
            '1',
            'arg1',
            'arg2',
        ),
    );

    // }}}

    // {{{   suite()

	public static function suite()
	{
		return new ezcTestSuite( "ezcConsoleToolsParameterTest" );
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
        $this->consoleParameter = new ezcConsoleParameter();
        foreach ( $this->testParams as $param )
        {
            $this->consoleParameter->registerParam( $param['short'], $param['long'], $param['options'] );
        }
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
        unset( $this->consoleParameter );
    }

    // }}} 

    // {{{ testRegisterParam

    /**
     * testRegisterParam
     * 
     * @access public
     * @return 
     */
    public function testRegisterParam()
    {
        // Using local object to test registration itself.
        $tmpConsoleParameter = new ezcConsoleParameter();
        foreach ( $this->testParams as $param )
        {
            $tmpConsoleParameter->registerParam( $param['short'], $param['long'], $param['options'] );
            $this->assertEquals( 
                array_merge( $tmpConsoleParameter->getDefaults(), $param['options'] ),
                $tmpConsoleParameter->getParamDef( $param['short'] ),
                'Parameter not registered correctly: "' . $param['short'] . '".'
            );
            $this->assertEquals( 
                array_merge( $tmpConsoleParameter->getDefaults(), $param['options'] ), 
                $tmpConsoleParameter->getParamDef( $param['long'] ),
                'Parameter not registered correctly: "' . $param['long'] . '".'
            );
        }
    }

    // }}}
    // {{{ testRegisterAlias

    /**
     * testRegisterAliasSuccess
     * 
     * @access public
     * @return 
     */
    public function testRegisterAliasSuccess()
    {
        foreach ( $this->testParams as $param )
        {
            $this->consoleParameter->registerParam( $param['short'], $param['long'], $param['options'] );
        }
        foreach ( $this->testAliasesSuccess as $alias )
        {
            try 
            {
                $this->consoleParameter->registerAlias( $alias['short'], $alias['long'], $alias['ref'] );
            }
            catch ( ezcConsoleParameterException $e )
            {
                $this->fail( $e->getMessage() );
            }
        }
    }
    
    /**
     * testRegisterAliasFailure
     * 
     * @access public
     * @return 
     */
    public function testRegisterAliasFailure()
    {
        $exceptionCount = 0;
        foreach ( $this->testAliasesFailure as $alias )
        {
            try 
            {
                $this->consoleParameter->registerAlias( $alias['short'], $alias['long'], $alias['ref'] );
            }
            catch ( ezcConsoleParameterException $e )
            {
                if ( $e->getCode() !== ezcConsoleParameterException::CODE_EXISTANCE )
                {
                    $this->fail( 'Alias registration threw unexpected exception "' . $e->getMessage()  . '" when registering alias for unknown parameter.' );
                }
                $exceptionCount++;
            }
        }
        // Expect every test data set to fail
        $this->assertEquals( 
            $exceptionCount,
            count( $this->testAliasesFailure ), 
            'Alias registration succeded for ' . ( count( $this->testAliasesFailure ) - $exceptionCount ) . ' unkown parameters.' 
        );
    }

    // }}}
    // {{{ test process()- success

    // Single parameter tests

    public function testProcessSuccessSingleShortNoValue()
    {
        $args = array(
            'foo.php',
            '-t',
        );
        $this->commonProcessTestSuccess( $args );
    }
    
    public function testProcessSuccessSingleShortValue()
    {
        $args = array(
            'foo.php',
            '-o',
            'bar'
        );
        $this->commonProcessTestSuccess( $args );
    }
    
    public function testProcessSuccessSingleLongNoValue()
    {
        $args = array(
            'foo.php',
            '--testing',
        );
        $this->commonProcessTestSuccess( $args );
    }
    
    public function testProcessSuccessSingleLongValue()
    {
        $args = array(
            'foo.php',
            '--original',
            'bar'
        );
        $this->commonProcessTestSuccess( $args );
    }

    public function testProcessSuccessSingleShortDefault()
    {
        $args = array(
            'foo.php',
            '-b'
        );
        $this->commonProcessTestSuccess( $args );
    }
    
    public function testProcessSuccessSingleLongDefault()
    {
        $args = array(
            'foo.php',
            '--build'
        );
        $this->commonProcessTestSuccess( $args );
    }

    // Multiple parameter tests
    
    public function testProcessSuccessMultipleShortNoValue()
    {
        $args = array(
            'foo.php',
            '-t',
            '-s',
        );
        $this->commonProcessTestSuccess( $args );
    }
    
    public function testProcessSuccessMultipleShortValue()
    {
        $args = array(
            'foo.php',
            '-o',
            'bar',
            '-b',
            '23'
        );
        $this->commonProcessTestSuccess( $args );
    }
    
    public function testProcessSuccessMultipleLongNoValue()
    {
        $args = array(
            'foo.php',
            '--testing',
            '--subway',
        );
        $this->commonProcessTestSuccess( $args );
    }
    
    public function testProcessSuccessMultipleLongValue()
    {
        $args = array(
            'foo.php',
            '--original',
            'bar',
            '--build',
            '23',
        );
        $this->commonProcessTestSuccess( $args );
    }
    
    public function testProcessSuccessMultipleShortDefault()
    {
        $args = array(
            'foo.php',
            '-b',
            '-d',
        );
        $this->commonProcessTestSuccess( $args );
    }
    
    public function testProcessSuccessMultipleLongDefault()
    {
        $args = array(
            'foo.php',
            '--build',
            '--destroy',
        );
        $this->commonProcessTestSuccess( $args );
    }

    // }}}

    // {{{ Helper methods

    private function commonProcessTestSuccess( $args )
    {
        try 
        {
            $this->consoleParameter->process( $args );
        }
        catch ( ezcConsoleParameterException $e )
        {
            $this->fail( $e->getMessage() );
        }
    }
    
    private function commonProcessTestFailure( $args )
    {
        try 
        {
            $this->consoleParameter->process( $args );
        }
        catch ( ezcConsoleParameterException $e )
        {
            return;
        }
        $this->fail( 'Exception not thrown for invalid parameter submition.' );
    }
    
    // }}}

}

?>
