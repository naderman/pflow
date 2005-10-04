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

    // {{{ $testParams

    private $testParams = array( 
        array( 
            'short'     => 't',
            'long'      => 'testing',
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
            'short'     => 'o',
            'long'      => 'order',
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

    // {{{ testRegisterParam()

    /**
     * testRegisterParam
     * 
     * @access public
     * @return 
     */
    public function testRegisterParam()
    {
        foreach ( $this->testParams as $param )
        {
            $this->consoleParameter->registerParam( $param['short'], $param['long'], $param['options'] );
            $this->assertTrue( 
                array_merge( $this->consoleParameter->getDefaults(), $param['options'] ) == $this->consoleParameter->getParamDef( $param['short'] ),
                'Parameter not registered correctly: "' . $param['short'] . '".'
            );
            $this->assertTrue( 
                array_merge( $this->consoleParameter->getDefaults(), $param['options'] ) == $this->consoleParameter->getParamDef( $param['long'] ),
                'Parameter not registered correctly: "' . $param['long'] . '".'
            );
        }
    }

    // }}}
    // {{{ testRegisterAlias()

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
                $this->assertTrue( false, 'Alias registration failed for parameter "' . $alias['ref'] . '" although it should work.' );
            }
        }
        $this->assertTrue( true );
    }
    
    /**
     * testRegisterAliasFailure
     * 
     * @access public
     * @return 
     */
    public function testRegisterAliasFailure()
    {
        foreach ( $this->testParams as $param )
        {
            $this->consoleParameter->registerParam( $param['short'], $param['long'], $param['options'] );
        }
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
                    $this->assertTrue( false, 'Alias threw wrong exception code "' . $e->getCode()  . '" when registering alias for unknown parameter.' );
                }
                $exceptionCount++;
            }
        }
        $this->assertTrue( 
            $exceptionCount == count( $this->testAliasesFailure ), 
            'Alias registration succeded for ' . ( count( $this->testAliasesFailure ) - $exceptionCount ) . ' unkown parameters.' 
        );
    }

    // }}}
}

?>
