<?php
/**
 * @package Base
 * @subpackage Tests
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 */
/**
 * @package Base
 * @subpackage Tests
 */
class ezcBaseTest extends ezcTestCase
{
    public function testAssertType()
    {
        $base = new ezcBase();
        ezcBase::assertType($base, "ezcBase");
        
        try
        {
            $a = array();
            ezcBase::assertType($a, "ezcBase");
            $this->fail("Expected an ezcBaseTypeException from 'array'");
        }
        catch ( ezcBaseTypeException $e)
        {
            $this->assertTrue(true);
        }

        try
        {
            $baseTest = new ezcBaseTest();
            ezcBase::assertType($a, "ezcBase");
            $this->fail("Expected an ezcBaseTypeException from 'ezcBaseTest'");
        }
        catch ( ezcBaseTypeException $e)
        {
            $this->assertTrue(true);
        }
    }

    public function testConfigExceptionUnknownSetting()
    {
        try
        {
            throw new ezcBaseConfigException( 'broken', ezcBaseConfigException::UNKNOWN_CONFIG_SETTING );
        }
        catch ( ezcBaseConfigException $e )
        {
            $this->assertEquals( "The setting 'broken' is not a valid configuration setting.", $e->getMessage() );
        }
    }

    public function testConfigExceptionOutOfRange()
    {
        try
        {
            throw new ezcBaseConfigException( 'broken', ezcBaseConfigException::VALUE_OUT_OF_RANGE, 42 );
        }
        catch ( ezcBaseConfigException $e )
        {
            $this->assertEquals( "The value '42' that you were trying to assign to setting 'broken' is invalid.", $e->getMessage() );
        }
    }

    public static function suite()
    {
        return new ezcTestSuite("ezcBaseTest");
    }
}
?>
