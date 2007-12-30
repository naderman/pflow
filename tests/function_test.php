<?php
/**
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package Reflection
 * @subpackage Tests
 */

class ezcReflectionFunctionTest extends ezcTestCase
{
	/**
     * @var ezcReflectionFunction
     */
    protected $fctM1;
    protected $fctM2;
    protected $fctM3;

    public function setUp() {
        $this->fctM1 = new ezcReflectionFunction('m1');
        $this->fctM2 = new ezcReflectionFunction('m2');
        $this->fctM3 = new ezcReflectionFunction('m3');
    }

    public function tearDown() {
        unset($this->fctM1);
        unset($this->fctM2);
        unset($this->fctM3);
    }
	
    public function testGetTags() {
        $func = $this->fctM1;
        $tags = $func->getTags();

        $expectedTags = array('webmethod', 'author', 'param', 'param', 'param', 'return');
        ReflectionTestHelper::expectedTags($expectedTags, $tags, $this);


        $func = $this->fctM2;
        $tags = $func->getTags();
        $expectedTags = array('param', 'author');
        ReflectionTestHelper::expectedTags($expectedTags, $tags, $this);
    }

    public function testIsTagged() {
        $func = $this->fctM1;
        self::assertFalse($func->isTagged('licence'));
        self::assertTrue($func->isTagged('webmethod'));
    }

    public function testGetLongDescription() {
        $func = $this->fctM1;
        $desc = $func->getLongDescription();

        $expected = '';
        self::assertEquals($expected, $desc);

        $func = $this->fctM2;
        $desc = $func->getLongDescription();

        $expected = '';
        self::assertEquals($expected, $desc);

        $func = $this->fctM3;
        $desc = $func->getLongDescription();

        $expected = '';
        self::assertEquals($expected, $desc);

        $func = new ezcReflectionFunction('m4');
        $desc = $func->getLongDescription();

        $expected =  "This function is used to set up the DOM-Tree and to make the important\n".
                     "nodes accessible by assigning global variables to them. Furthermore,\n".
                     "depending on the used \"USE\", diferent namespaces are added to the\n".
                     "definition element.\n".
                     "Important: the nodes are not appended now, because the messages are not\n".
                     "created yet. That's why they are appended after the messages are created.";
        self::assertEquals($expected, $desc);
    }

    public function testGetShortDescription() {
        $func = $this->fctM1;
        $desc = $func->getShortDescription();
        $expected = 'To check whether a tag was used';
        self::assertEquals($expected, $desc);

        $func = $this->fctM2;
        $desc = $func->getShortDescription();
        $expected = '';
        self::assertEquals($expected, $desc);

        $func = $this->fctM3;
        $desc = $func->getShortDescription();
        $expected = '';
        self::assertEquals($expected, $desc);

        $func = new ezcReflectionFunction('m4');
        $desc = $func->getShortDescription();
        $expected = 'Enter description here...';
        self::assertEquals($expected, $desc);
    }

    public function testIsWebmethod() {
        $func = $this->fctM1;
        self::assertTrue($func->isWebmethod());

        $func = $this->fctM2;
        self::assertFalse($func->isWebmethod());
    }

    public function testGetReturnDescription() {
        $func = $this->fctM1;
        $desc = $func->getReturnDescription();
        self::assertEquals('Hello World', $desc);

        $func = new ezcReflectionFunction('m4');
        $desc = $func->getReturnDescription();
        self::assertEquals('', $desc);
    }

    public function testGetReturnType() {
        $func = new ezcReflectionFunction('m1');
        $type = $func->getReturnType();
        self::assertType('ezcReflectionType', $type);
        self::assertEquals('string', $type->toString());

        $func = new ezcReflectionFunction('m4');
        self::assertNull($func->getReturnType());
    }

    public function testGetParameters() {
        $func = new ezcReflectionFunction('m1');
        $params = $func->getParameters();

        $expected = array('test', 'test2', 'test3');
        ReflectionTestHelper::expectedParams($expected, $params, $this);

        $func = $this->fctM3;
        $params = $func->getParameters();
        self::assertTrue(count($params) == 0);
    }

    public function testGetName() {
    	self::assertEquals('m1', $this->fctM1->getName());
    	self::assertEquals('m2', $this->fctM2->getName());
    }
       
    public function testIsInternal() {
    	self::assertFalse($this->fctM1->isInternal());
    }
    
    public function testIsDisabled() {
    	self::assertFalse($this->fctM1->isDisabled());
    }
    
    public function testIsUserDefined() {
    	self::assertTrue($this->fctM1->isUserDefined());
    }
    
    public function testGetFileName() {
    	self::assertEquals('functions.php', basename($this->fctM1->getFileName()));
    }
    
    public function testGetStartLine() {
    	self::assertEquals(12, $this->fctM1->getStartLine());
    }
    
    public function testGetEndLine() {
    	self::assertEquals(14, $this->fctM1->getEndLine());
    }
    
    public function testGetDocComment() {
    	self::assertEquals("/**
 * @param void \$DocuFlaw
 * @author flaw joe
 */", $this->fctM2->getDocComment());
    }
    
    public function testGetStaticVariables() {
    	$vars = $this->fctM3->getStaticVariables();
    	self::assertEquals(1, count($vars));
    	self::assertTrue(array_key_exists('staticVar', $vars));
    }
    
    //public mixed invoke([mixed args [, ...]])
    //public mixed invokeArgs(array args)
    
    public function testReturnsReference() {
    	self::assertFalse($this->fctM3->returnsReference());
    }
    
    public function testGetNumberOfParameters() {
    	self::assertEquals(3, $this->fctM1->getNumberOfParameters());
    	$func = new ReflectionFunction('mmm');
    	self::assertEquals(1, $func->getNumberOfParameters());
    }
    public function testGetNumberOfRequiredParameters() {
    	self::assertEquals(3, $this->fctM1->getNumberOfRequiredParameters());
    	$func = new ReflectionFunction('mmm');
    	self::assertEquals(0, $func->getNumberOfRequiredParameters());
    }
    
    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( "ezcReflectionFunctionTest" );
    }
}
?>
