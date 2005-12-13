<?php
/**
 * ezcConsoleToolsParameterTest
 * 
 * @package ConsoleTools
 * @subpackage Tests
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Test suite for ezcConsoleParameter class.
 * 
 * @package ConsoleTools
 * @subpackage Tests
 */
class ezcConsoleToolsParameterTest extends ezcTestCase
{
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
            'short'     => 'v',
            'long'      => 'visual',
            'options'   => array(
                'multiple'  => true,
                'arguments' => false,
            ),
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
                'shorthelp'     => 'Some stupid short text.',
                'longhelp'      => 'Some even more stupid, but somewhat longer long describtion.',
            ),
        ),
        array( 
            'short'     => 'c',
            'long'      => 'console',
            'options'   => array(
                'shorthelp'     => 'Some stupid short text.',
                'longhelp'      => 'Some even more stupid, but somewhat longer long describtion.',
                'depends'       => array( 't', 'o', 'b', 'y' ),
            ),
        ),
        array( 
            'short'     => 'e',
            'long'      => 'edit',
            'options'   => array(
                'excludes'      => array( 't', 'y' ),
                'arguments'     => false,
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

    private $testAliasesSuccess = array( 
        array(
            'short' => 'k',
            'long'  => 'kelvin',
            'ref'   => 't',
        ),
        array(
            'short' => 'f',
            'long'  => 'foobar',
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

	public static function suite()
	{
		return new ezcTestSuite( "ezcConsoleToolsParameterTest" );
	}

    /**
     * setUp 
     * 
     * @access public
     */
    public function setUp()
    {
        $this->consoleParameter = new ezcConsoleParameter();
        foreach ( $this->testParams as $paramData )
        {
            $this->consoleParameter->registerParam( $this->createFakeParam( $paramData ) );
        }
    }

    protected function createFakeParam( $paramData )
    {
        $param = new ezcConsoleParameterStruct( $paramData['short'], $paramData['long'] );
        foreach( $paramData['options'] as $name => $val )
        {
            if ( $name === 'depends' )
            {
                foreach ( $val as $dep )
                {
                    $param->addDependency(new ezcConsoleParameterRule($this->consoleParameter->getParam($dep)));
                }
                continue;
            }
            if ( $name === 'excludes' )
            {
                foreach ( $val as $dep )
                {
                    $param->addExclusion(new ezcConsoleParameterRule($this->consoleParameter->getParam($dep)));
                }
                continue;
            }
            $param->$name = $val;
        }
        return $param;
    }

    /**
     * tearDown 
     * 
     * @access public
     */
    public function tearDown()
    {
        unset( $this->consoleParameter );
    }

    /**
     * testRegisterParam
     * 
     * @access public
     */
    public function testRegisterParam()
    {
        // Using local object to test registration itself.
        $tmpConsoleParameter = new ezcConsoleParameter();
        foreach ( $this->testParams as $paramData )
        {
            $param = $this->createFakeParam( $paramData );
            $tmpConsoleParameter->registerParam( $param );
            $this->assertEquals( 
                $param,
                $tmpConsoleParameter->getParam( $paramData['short'] ),
                'Parameter not registered correctly with short name <' . $paramData['short'] . '>.'
            );
            $this->assertEquals( 
                $param,
                $tmpConsoleParameter->getParam( $paramData['long'] ),
                'Parameter not registered correctly with long name <' . $paramData['long'] . '>.'
            );
        }
    }

    public function testFromString()
    {
        $param = new ezcConsoleParameter();
        $param->fromString( '[a:|all:][u?|user?][i|info][o+test|overall+]' );
        $res['a'] = new ezcConsoleParameterStruct(
            'a', 
            'all', 
            ezcConsoleParameter::TYPE_NONE, 
            NULL, 
            false, 
            'No help available.', 
            'Sorry, there is no help text available for this parameter.', 
            array(), 
            array (), 
            true 
        );
        $res['u'] = new ezcConsoleParameterStruct(
            'u',
            'user',
            ezcConsoleParameter::TYPE_STRING,
            '',
            false,
            'No help available.',
            'Sorry, there is no help text available for this parameter.',
            array (),
            array (),
            true
        );
        $res['o'] = new ezcConsoleParameterStruct(
            'o',
            'overall',
            ezcConsoleParameter::TYPE_STRING,
            'test',
            true,
            'No help available.',
            'Sorry, there is no help text available for this parameter.',
            array (),
            array (),
            true
        );
        $this->assertEquals( $res['a'], $param->getParam( 'a' ), 'Parameter -a not registered correctly.'  );
        $this->assertEquals( $res['u'], $param->getParam( 'u' ), 'Parameter -u not registered correctly.'  );
        $this->assertEquals( $res['o'], $param->getParam( 'o' ), 'Parameter -o not registered correctly.'  );
    }

    /**
     * testRegisterAliasSuccess
     * 
     * @access public
     */
    public function testRegisterAliasSuccess()
    {
        $validParams = array();
        foreach ( $this->consoleParameter->getParams() as $param )
        {
            $validParams[$param->short] = $param;
        }
        foreach ( $this->testAliasesSuccess as $alias )
        {
            try 
            {
                $this->consoleParameter->registerAlias( $alias['short'], $alias['long'], $validParams[$alias['ref']]  );
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
     */
    public function testRegisterAliasFailure()
    {
        $exceptionCount = 0;
        foreach ( $this->testAliasesFailure as $alias )
        {
            try 
            {
                $this->consoleParameter->registerAlias( $alias['short'], $alias['long'], new ezcConsoleParameterStruct('foo', 'bar') );
            }
            catch ( ezcConsoleParameterException $e )
            {
                if ( $e->getCode() !== ezcConsoleParameterException::PARAMETER_NOT_EXISTS )
                {
                    $this->fail( 'Alias registration threw unexpected exception <' . $e->getMessage() . '> when registering alias for unknown parameter.' );
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

    // Single parameter tests
    public function testProcessSuccessSingleShortNoValue()
    {
        $args = array(
            'foo.php',
            '-t',
        );
        $res = array( 
            't' => true,
        );
        $this->commonProcessTestSuccess( $args, $res );
    }
    
    public function testProcessSuccessSingleShortValue()
    {
        $args = array(
            'foo.php',
            '-o',
            'bar'
        );
        $res = array( 
            'o' => 'bar',
        );
        $this->commonProcessTestSuccess( $args, $res );
    }
    
    public function testProcessSuccessSingleLongNoValue()
    {
        $args = array(
            'foo.php',
            '--testing',
        );
        $res = array( 
            't' => true,
        );
        $this->commonProcessTestSuccess( $args, $res );
    }
    
    public function testProcessSuccessSingleLongValue()
    {
        $args = array(
            'foo.php',
            '--original',
            'bar'
        );
        $res = array( 
            'o' => 'bar',
        );
        $this->commonProcessTestSuccess( $args, $res );
    }

    public function testProcessSuccessSingleShortDefault()
    {
        $args = array(
            'foo.php',
            '-b'
        );
        $res = array( 
            'b' => 42,
        );
        $this->commonProcessTestSuccess( $args, $res );
    }
    
    public function testProcessSuccessSingleLongDefault()
    {
        $args = array(
            'foo.php',
            '--build'
        );
        $res = array( 
            'b' => 42,
        );
        $this->commonProcessTestSuccess( $args, $res );
    }

    public function testProcessSuccessSingleShortNoValueArguments()
    {
        $args = array(
            'foo.php',
            '-s',
            '--',
            '-foo',
            '--bar',
            'baz',
        );
        $res = array( 
            's' => true,
        );
        $this->commonProcessTestSuccess( $args, $res );
    }
    
    public function testProcessSuccessSingleLongNoValueArguments()
    {
        $args = array(
            'foo.php',
            '--subway',
            '--',
            '-foo',
            '--bar',
            'baz',
        );
        $res = array( 
            's' => true,
        );
        $this->commonProcessTestSuccess( $args, $res );
    }

    // Multiple parameter tests
    public function testProcessSuccessMultipleShortNoValue()
    {
        $args = array(
            'foo.php',
            '-t',
            '-s',
        );
        $res = array( 
            't' => true,
            's' => true,
        );
        $this->commonProcessTestSuccess( $args, $res );
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
        $res = array( 
            'o' => 'bar',
            'b' => 23,
        );
        $this->commonProcessTestSuccess( $args, $res );
    }
    
    public function testProcessSuccessMultipleLongNoValue()
    {
        $args = array(
            'foo.php',
            '--testing',
            '--subway',
        );
        $res = array( 
            't' => true,
            's' => true,
        );
        $this->commonProcessTestSuccess( $args, $res );
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
        $res = array( 
            'o' => 'bar',
            'b' => 23,
        );
        $this->commonProcessTestSuccess( $args, $res );
    }
    
    public function testProcessSuccessMultipleShortDefault()
    {
        $args = array(
            'foo.php',
            '-b',
            '-d',
        );
        $res = array( 
            'b' => 42,
            'd' => 'world',
        );
        $this->commonProcessTestSuccess( $args, $res );
    }
    
    public function testProcessSuccessMultipleLongDefault()
    {
        $args = array(
            'foo.php',
            '--build',
            '--destroy',
        );
        $res = array( 
            'b' => 42,
            'd' => 'world',
        );
        $this->commonProcessTestSuccess( $args, $res );
    }
    
    public function testProcessSuccessMultipleLongSameNoValue()
    {
        $args = array(
            'foo.php',
            '--visual',
            '--visual',
        );
        $res = array( 
            'v' => array( true, true ),
        );
        $this->commonProcessTestSuccess( $args, $res );
    }

    public function testProcessSuccessArguments_1()
    {
        $args = array(
            'foo.php',
            '--original',
            'bar',
            '--build',
            '23',
            'argument',
            '1',
            '2',
        );
        $res = array( 
            0 => 'argument',
            1 => '1',
            2 => '2',
        );
        $this->argumentsProcessTestSuccess( $args, $res );
    }
    
    public function testProcessSuccessDependencies()
    {
        $args = array(
            'foo.php',
            '-t',
            '-o',
            'bar',
            '--build',
            '-y',
            'text',
            '--yank',
            'moretext',
            '-c'            // This one depends on -t, -o, -b and -y
        );
        $res = array( 
            't' => true,
            'o' => 'bar',
            'b' => 42,
            'y' => array( 
                'text',
                'moretext'
            ),
            'c' => true,
        );
        $this->commonProcessTestSuccess( $args, $res );
    }
    
    public function testProcessSuccessExclusions()
    {
        $args = array(
            'foo.php',
            '-o',
            'bar',
            '--build',
            '--edit'            // This one exclude -t and -y
        );
        $res = array( 
            'o' => 'bar',
            'b' => 42,
            'e' => true,
        );
        $this->commonProcessTestSuccess( $args, $res );
    }
    
    public function testProcessSuccessDependenciesExclusions()
    {
        $args = array(
            'foo.php',
            '-t',
            '-o',
            'bar',
            '-n'            // This one depends on -t and -o, but excludes -b and -y
        );
        $res = array( 
            't' => true,
            'o' => 'bar',
            'n' => true,
        );
        $this->commonProcessTestSuccess( $args, $res );
    }

    public function testProcessFailureExistance_1()
    {
        $args = array(
            'foo.php',
            '-q',
        );
        $this->commonProcessTestFailure( $args, ezcConsoleParameterException::PARAMETER_NOT_EXISTS );
    }
    
    public function testProcessFailureExistance_2()
    {
        $args = array(
            'foo.php',
            '-tools',
        );
        $this->commonProcessTestFailure( $args, ezcConsoleParameterException::PARAMETER_NOT_EXISTS );
    }
    
    public function testProcessFailureExistance_3()
    {
        $args = array(
            'foo.php',
            '-testingaeiou',
        );
        $this->commonProcessTestFailure( $args, ezcConsoleParameterException::PARAMETER_NOT_EXISTS );
    }
    
    public function testProcessFailureType()
    {
        $args = array(
            'foo.php',
            '-b',
            'not_an_int'
        );
        $this->commonProcessTestFailure( $args, ezcConsoleParameterException::PARAMETER_TYPE_RULE_NOT_MET );
    }
    
    public function testProcessFailureNovalue()
    {
        $args = array(
            'foo.php',
            '-o',
        );
        $this->commonProcessTestFailure( $args, ezcConsoleParameterException::MISSING_PARAMETER_VALUE );
    }
    
    public function testProcessFailureMultiple()
    {
        $args = array(
            'foo.php',
            '-d',
            'mars',
            '--destroy',
            'venus',
            
        );
        $this->commonProcessTestFailure( $args, ezcConsoleParameterException::TOO_MANY_PARAMETER_VALUES );
    }
    
    public function testProcessFailureDependencies()
    {
        $args = array(
            'foo.php',
            '-t',
//            '-o',
//            'bar',
            '--build',
            '-y',
            'text',
            '--yank',
            'moretext',
            '-c'            // This one depends on -t, -o, -b and -y
        );
        $this->commonProcessTestFailure( $args, ezcConsoleParameterException::PARAMETER_DEPENDENCY_RULE_NOT_MET );
    }
    
    public function testProcessFailureExclusions()
    {
        $args = array(
            'foo.php',
            '-t',
            '-o',
            'bar',
            '--build',
            '--edit'            // This one excludes -t and -y
        );
        $this->commonProcessTestFailure( $args, ezcConsoleParameterException::PARAMETER_EXCLUSION_RULE_NOT_MET );
    }
    
    public function testProcessFailureArguments()
    {
        $args = array(
            'foo.php',
            '-t',
            '--visual',         // This one forbids arguments
            '-o',
            'bar',
            'someargument',
        );
        $this->commonProcessTestFailure( $args, ezcConsoleParameterException::ARGUMENTS_NOT_ALLOWED );
    }

    public function testGetHelp1()
    {
        $res = array( 
            array( 
                '-t / --testing',
                'No help available.',
            ),
            array( 
                '-s / --subway',
                'No help available.',
            ),
            array( 
                '-v / --visual',
                'No help available.',
            ),
            array( 
                '-o / --original',
                'No help available.',
            ),
            array( 
                '-b / --build',
                'No help available.',
            ),
            array( 
                '-d / --destroy',
                'No help available.',
            ),
            array( 
                '-y / --yank',
                'Some stupid short text.',
            ),
            array( 
                '-c / --console',
                'Some stupid short text.',
            ),
            array( 
                '-e / --edit',
                'No help available.',
            ),
            array( 
                '-n / --new',
                'No help available.',
            ),
        );
        $this->assertEquals( 
            $res,
            $this->consoleParameter->getHelp(),
            'Help array was not generated correctly.'
        );
    }
    
    public function testGetHelp2()
    {
        $res = array( 
            array( 
                '-t / --testing',
                'Sorry, there is no help text available for this parameter.',
            ),
            array( 
                '-s / --subway',
                'Sorry, there is no help text available for this parameter.',
            ),
            array( 
                '-v / --visual',
                'Sorry, there is no help text available for this parameter.',
            ),
            array( 
                '-o / --original',
                'Sorry, there is no help text available for this parameter.',
            ),
            array( 
                '-b / --build',
                'Sorry, there is no help text available for this parameter.',
            ),
            array( 
                '-d / --destroy',
                'Sorry, there is no help text available for this parameter.',
            ),
            array( 
                '-y / --yank',
                'Some even more stupid, but somewhat longer long describtion.',
            ),
            array( 
                '-c / --console',
                'Some even more stupid, but somewhat longer long describtion.',
            ),
            array( 
                '-e / --edit',
                'Sorry, there is no help text available for this parameter.',
            ),
            array( 
                '-n / --new',
                'Sorry, there is no help text available for this parameter.',
            ),
        );
        $this->assertEquals( 
            $res,
            $this->consoleParameter->getHelp( true ),
            'Help array was not generated correctly.'
        );
        
    }
    
    public function testGetHelp3()
    {
        $res = array( 
            array( 
                '-t / --testing',
                'No help available.',
            ),
            array( 
                '-s / --subway',
                'No help available.',
            ),
            array( 
                '-v / --visual',
                'No help available.',
            ),
        );
        $this->assertEquals( 
            $res,
            $this->consoleParameter->getHelp(false, array( 't', 's', 'v' ) ),
            'Help array was not generated correctly.'
        );
    }
    
    public function testGetHelp4()
    {
        $res = array( 
            array( 
                '-t / --testing',
                'Sorry, there is no help text available for this parameter.',
            ),
            array( 
                '-s / --subway',
                'Sorry, there is no help text available for this parameter.',
            ),
            array( 
                '-y / --yank',
                'Some even more stupid, but somewhat longer long describtion.',
            ),
            array( 
                '-e / --edit',
                'Sorry, there is no help text available for this parameter.',
            ),
            array( 
                '-n / --new',
                'Sorry, there is no help text available for this parameter.',
            ),
        );
        $this->assertEquals( 
            $res,
            $this->consoleParameter->getHelp( true, array( 't', 'subway', 'yank', 'e', 'n' ) ),
            'Help array was not generated correctly.'
        );
        
    }

    private function commonProcessTestSuccess( $args, $res )
    {
        try 
        {
            $this->consoleParameter->process( $args );
        }
        catch ( ezcConsoleParameterException $e )
        {
            $this->fail( $e->getMessage() );
            return;
        }
        $values = $this->consoleParameter->getValues();
        $this->assertTrue( count( array_diff( $res, $values ) ) == 0, 'Parameters processed incorrectly.' );
    }
    
    private function commonProcessTestFailure( $args, $code )
    {
        try 
        {
            $this->consoleParameter->process( $args );
        }
        catch ( ezcConsoleParameterException $e )
        {
            $this->assertEquals(
                $code,
                $e->getCode(),
                'Wrong exception thrown for invalid parameter submission.'
            );
            return;
        }
        $this->fail( 'Exception not thrown for invalid parameter submition.' );
    }

    private function argumentsProcessTestSuccess( $args, $res )
    {
        try
        {
            $this->consoleParameter->process( $args );
        }
        catch ( ezcConsoleParameterException $e )
        {
            $this->fail( $e->getMessage() );
            return;
        }
        $this->assertEquals(
            $res,
            $this->consoleParameter->getArguments(),
            'Arguments not parsed correctly.'
        );
    }
}
?>
