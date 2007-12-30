<?php
/**
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package Reflection
 * @subpackage Tests
 */

class ezcReflectionPropertyTest extends ezcTestCase
{
    /**
     * @var ezcReflectionProperty
     */
    protected $refProp;

    public function setUp() {
        $class = new ezcReflectionClass('SomeClass');
		$this->refProp = $class->getProperty('fields');
    }

    public function tearDown() {
        unset($this->refProp);
    }

    public function testGetType() {
        $type = $this->refProp->getType();
        self::assertType('ezcReflectionArrayType', $type);
        self::assertEquals('integer[]', $type->toString());
    }

    public function testGetDeclaringClass() {
        $class = $this->refProp->getDeclaringClass();
        self::assertType('ezcReflectionClassType', $class);
        self::assertEquals('SomeClass', $class->toString());
    }
	
	public function testGetName() {
		self::assertEquals('fields', $this->refProp->getName());
	}
	
    public function testIsPublic() {
		self::assertFalse($this->refProp->isPublic());
	}
	
	public function testIsPrivate() {
		self::assertTrue($this->refProp->isPrivate());
	}
    
	public function testIsProtected() {
		self::assertFalse($this->refProp->isProtected());
	}
	
	public function testIsStatic() {
		self::assertFalse($this->refProp->isStatic());
	}
    
	public function testIsDefault() {
		self::assertTrue($this->refProp->isDefault());
	}
    
	public function testGetModifiers() {
		self::assertEquals(1024, $this->refProp->getModifiers());
	}
	
	/**
	* @expectedException ReflectionException
	*/
	public function testGetValue() {
		$o = new SomeClass();
		self::assertEquals(null, $this->refProp->getValue($o));
	}
	
	/**
	* @expectedException ReflectionException
	*/
	public function testSetValue() {
		$o = new SomeClass();
		//self::assertEquals(null, $this->refProp->getValue($o));
		$this->refProp->setValue($o, 3);
		//self::assertEquals(3, $this->refProp->getValue($o));
	}
	
	public function testGetDocComment() {
		$o = new SomeClass();
		self::assertEquals("/**
	* @var int[]
	*/", $this->refProp->getDocComment($o));
	}
	

    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( "ezcReflectionPropertyTest" );
    }
}
?>