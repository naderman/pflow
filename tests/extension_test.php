<?php
/**
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package Reflection
 * @subpackage Tests
 */

class ezcReflectionExtensionTest extends ezcTestCase
{
	/**
	 * @var ezcReflectionExtension
	 */
	private $extRef;
	private $extSpl;
	
    public function setUp() {
        $this->extRef = new ezcReflectionExtension('Reflection');
        $this->extSpl = new ezcReflectionExtension('Spl');
    }

    public function tearDown() {
        unset($this->extRef);
        unset($this->extSpl);
    }
	
    public function testGetFunctions() {
        $functs = $this->extRef->getFunctions();
        foreach ($functs as $func) {
            self::assertType('ezcReflectionFunction', $func);
        }
        self::assertEquals(0, count($functs));
    }

    public function testGetClasses() {
        $classes = $this->extRef->getClasses();

        foreach ($classes as $class) {
            self::assertType('ezcReflectionClassType', $class);
        }
    }
    
    public function testGetName() {
    	self::assertEquals('SPL', $this->extSpl->getName());
    	self::assertEquals('Reflection', $this->extRef->getName());
    }
    
    public function testGetVersion() {
    	$version = $this->extRef->getVersion();
    	self::assertFalse(empty($version));
    }
    
    public function testInfo() {
    	ob_start();
    	$this->extRef->info();
    	$info = ob_get_clean(); 
    	self::assertFalse(empty($info));
    }
    
    public function testGetConstants() {
    	$constants = $this->extRef->getConstants();
    	self::assertTrue(empty($constants));
    }
    
    public function testGetINIEntries() {
    	$iniEntries = $this->extRef->getINIEntries();
    	self::assertTrue(empty($iniEntries));
    }
    
    public function testGetClassNames() {
    	$classNames = $this->extRef->getClassNames();
    	self::assertFalse(empty($classNames));
    }
    
    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( "ezcReflectionExtensionTest" );
    }
}
?>
