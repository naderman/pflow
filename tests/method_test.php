<?php
/**
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogen//
 * @filesource
 * @package Reflection
 * @subpackage Tests
 */

class ezcReflectionMethodTest extends ezcReflectionFunctionTest
{
	public function setUp() {
        $this->php_fctM1 = new ReflectionMethod('TestMethods', 'm1');
        $this->php_fctM2 = new ReflectionMethod('TestMethods', 'm2');
        $this->php_fctM3 = new ReflectionMethod('TestMethods', 'm3');
        $this->php_fct_method_exists = new ReflectionMethod( 'ReflectionClass', 'hasMethod' );
        $this->fctM1 = new ezcReflectionMethod('TestMethods', 'm1');
        $this->fctM2 = new ezcReflectionMethod('TestMethods', 'm2');
        $this->fctM3 = new ezcReflectionMethod('TestMethods', 'm3');
        $this->fct_method_exists = new ezcReflectionMethod( 'ReflectionClass', 'hasMethod' );
    }

    public function testGetDeclaringClass() {
        $class = $this->fctM1->getDeclaringClass();
        self::assertType('ezcReflectionClassType', $class);
        self::assertEquals('TestMethods', $class->getName());
    }

    public function testIsMagic() {
        self::assertFalse($this->fctM1->isMagic());

        $class = $this->fctM1->getDeclaringClass();
        self::assertTrue($class->getConstructor()->isMagic());
    }

    public function testGetTags() {
        $class = new ezcReflectionClass('ezcReflectionClass');
        $method = $class->getMethod('getMethod');
        $tags = $method->getTags();
        self::assertEquals(2, count($tags));


        $method = new ezcReflectionMethod('TestMethods', 'm4');
        $tags = $method->getTags();
        $expectedTags = array('webmethod', 'restmethod', 'restin', 'restout', 'author', 'param', 'param', 'param', 'return');
        ReflectionTestHelper::expectedTags($expectedTags, $tags, $this);

        $tags = $method->getTags('param');
        $expectedTags = array('param', 'param', 'param');
        ReflectionTestHelper::expectedTags($expectedTags, $tags, $this);

        $method = new ezcReflectionMethod('TestMethods', 'm1');
        $tags = $method->getTags();
        $expectedTags = array('param', 'author');
        ReflectionTestHelper::expectedTags($expectedTags, $tags, $this);
    }

    public function testIsTagged() {
        $method = new ezcReflectionMethod('TestMethods', 'm4');
        self::assertTrue($method->isTagged('webmethod'));
        self::assertFalse($method->isTagged('fooobaaar'));
    }

    public function testGetLongDescription() {
        $desc = $this->fctM3->getLongDescription();

        $expected = "This is the long description with may be additional infos and much more lines\nof text.\n\nEmpty lines are valide to.\n\nfoo bar";
        self::assertEquals($expected, $desc);
    }

    public function testGetShortDescription() {
        $desc = $this->fctM3->getShortDescription();

        $expected = "This is the short description";
        self::assertEquals($expected, $desc);
    }

    public function testGetReturnDescription() {
        $method = new ezcReflectionMethod('TestMethods', 'm4');
        $desc = $method->getReturnDescription();
        self::assertEquals('Hello World', $desc);
    }

    public function testGetReturnType() {
        $method = new ezcReflectionMethod('TestMethods', 'm4');
        $type = $method->getReturnType();
        self::assertType('ezcReflectionType', $type);
        self::assertEquals('string', $type->toString());
    }

    public function testGetParameters() {
        $method = new ezcReflectionMethod('ezcReflectionMethod', 'getTags');
        $params = $method->getParameters();

        $expectedParams = array('name');
        foreach ($params as $param) {
            self::assertType('ezcReflectionParameter', $param);
            self::assertContains($param->getName(), $expectedParams);

            ReflectionTestHelper::deleteFromArray($param->getName(), $expectedParams);
        }
        self::assertEquals(0, count($expectedParams));
    }

    public function testIsInherited() {
        $method = new ezcReflectionMethod('TestMethods2', 'm2');
        self::assertFalse($method->isInherited());

        //is internal has been inherited an not redefined from ReflectionFunction
        $method = new ezcReflectionMethod('ReflectionMethod', 'isInternal');
        self::assertTrue($method->isInherited());

        $method = new ezcReflectionMethod('TestMethods2', 'm3');
        self::assertTrue($method->isInherited());

        $method = new ezcReflectionMethod('TestMethods2', 'newMethod');
        self::assertFalse($method->isInherited());

        $method = new ezcReflectionMethod('ezcReflectionMethod', 'isInherited');
        self::assertFalse($method->isInherited());
    }

    public function testIsOverriden() {
        $method = new ezcReflectionMethod('TestMethods2', 'm2');
        self::assertTrue($method->isOverridden());

        $method = new ezcReflectionMethod('TestMethods2', 'newMethod');
        self::assertFalse($method->isOverridden());

        $method = new ezcReflectionMethod('TestMethods2', 'm4');
        self::assertFalse($method->isOverridden());

        $method = new ezcReflectionMethod('ezcReflectionMethod', 'isInternal');
        self::assertFalse($method->isOverridden());
    }

    public function testIsIntroduced() {
        $method = new ezcReflectionMethod('TestMethods2', 'm2');
        self::assertFalse($method->isIntroduced());

        $method = new ezcReflectionMethod('TestMethods2', 'newMethod');
        self::assertTrue($method->isIntroduced());

        $method = new ezcReflectionMethod('TestMethods2', 'm4');
        self::assertFalse($method->isIntroduced());
    }

	public function testIsDisabled() {
    	// is not available for methods
    }

	public function testGetFileName() {
    	self::assertEquals('methods.php', basename($this->fctM1->getFileName()));
    }

    public function testGetStartLine() {
    	self::assertEquals(16, $this->fctM1->getStartLine());
    }

    public function testGetEndLine() {
    	self::assertEquals(18, $this->fctM1->getEndLine());
    }

	public function testGetDocComment() {
    	self::assertEquals("/**
     * @foo
     * @bar
     * @foobar
     */", $this->fctM2->getDocComment());
    }

    public function testInvoke() {
        self::assertEquals(
            $this->php_fct_method_exists->invoke( new ReflectionClass('ReflectionClass'), 'hasMethod' ),
            $this->fct_method_exists->invoke( new ReflectionClass('ReflectionClass'), 'hasMethod' )
        );
    }

    public function testInvokeArgs() {
        self::assertEquals(
            $this->php_fct_method_exists->invokeArgs( new ReflectionClass('ReflectionClass'), array( 'hasMethod' ) ),
            $this->fct_method_exists->invokeArgs( new ReflectionClass('ReflectionClass'), array( 'hasMethod' ) )
        );
    }

	public function testGetNumberOfParameters() {
    	self::assertEquals(1, $this->fctM3->getNumberOfParameters());
    	self::assertEquals(0, $this->fctM1->getNumberOfParameters());
    }

    public function testGetNumberOfRequiredParameters() {
    	self::assertEquals(0, $this->fctM1->getNumberOfRequiredParameters());
    	self::assertEquals(1, $this->fctM3->getNumberOfRequiredParameters());
    }

    public function testIsFinal() {
    	self::assertFalse($this->fctM1->isFinal());
    	self::assertFalse($this->fctM2->isFinal());
    }

	public function testIsAbstract() {
    	self::assertFalse($this->fctM1->isAbstract());
    	self::assertFalse($this->fctM2->isAbstract());
    }

	public function testIsPublic() {
    	self::assertTrue($this->fctM1->isPublic());
    	self::assertTrue($this->fctM2->isPublic());
    }

	public function testIsPrivate() {
    	self::assertFalse($this->fctM1->isPrivate());
    	self::assertFalse($this->fctM2->isPrivate());
    }

	public function testIsProtected() {
    	self::assertFalse($this->fctM1->isProtected());
    	self::assertFalse($this->fctM2->isProtected());
    }

	public function testIsStatic() {
    	self::assertFalse($this->fctM1->isStatic());
    	self::assertFalse($this->fctM2->isStatic());
    }

	public function testIsConstructor() {
    	self::assertFalse($this->fctM1->isConstructor());
    	self::assertFalse($this->fctM2->isConstructor());
    }

	public function testIsDestructor() {
    	self::assertFalse($this->fctM1->isDestructor());
    	self::assertFalse($this->fctM2->isDestructor());
    }

	public function testGetModifiers() {
    	self::assertEquals(65792, $this->fctM1->getModifiers());
    	self::assertEquals(65792, $this->fctM2->getModifiers());
    }

    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( "ezcReflectionMethodTest" );
    }
}
?>
