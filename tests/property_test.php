<?php
/**
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogen//
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
		$this->refPropName = 'fields';
		$this->refProp = $class->getProperty($this->refPropName);
        $this->publicPropertyName = 'publicProperty';
        $this->publicProperty = $class->getProperty($this->publicPropertyName);
        $this->instanceOfSomeClass = new SomeClass();
        /*
        foreach ( $class->getProperties() as $property ) {
            if ( $property->getName() == 'fields' ) {
		        $this->refProp = $property;
                break;
            }
        }
        */
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

    public function testIsTagged() {
        self::assertTrue($this->refProp->isTagged('var'));
        self::assertFalse($this->refProp->isTagged('nonExistingAnnotation'));
    }

    public function testGetTags() {
        $expectedTags = array('var');

        $tags = $this->refProp->getTags();
        ReflectionTestHelper::expectedTags($expectedTags, $tags, $this);

        $tags = $this->refProp->getTags('var');
        ReflectionTestHelper::expectedTags($expectedTags, $tags, $this);
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

	public function testGetValue() {
		$o = $this->instanceOfSomeClass;
        $value = new SomeClass();
		self::assertEquals(null, $this->publicProperty->getValue($o));
        $propertyName = $this->publicPropertyName;
	    $o->$propertyName = $value;
		self::assertSame($value, $this->publicProperty->getValue($o));
    }

	/**
     * @expectedException ReflectionException
     */
	public function testGetValueOfPrivatePropertyThrowsException() {
		$this->refProp->getValue($this->instanceOfSomeClass);
	}

	public function testSetValue() {
		$o = $this->instanceOfSomeClass;
        $value = $this->instanceOfSomeClass;
        $propertyName = $this->publicPropertyName;
		self::assertEquals(null, $o->$propertyName);
		$this->publicProperty->setValue($o, $value);
		self::assertSame($value, $o->$propertyName);
	}

	/**
     * @expectedException ReflectionException
     */
	public function testSetValueOfPrivatePropertyThrowsException() {
		$this->refProp->setValue($this->instanceOfSomeClass, 3);
	}

	public function testGetDocComment() {
		self::assertEquals("/**
     * @var int[]
     */", $this->refProp->getDocComment($this->instanceOfSomeClass));
	}

    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( "ezcReflectionPropertyTest" );
    }
}
?>
