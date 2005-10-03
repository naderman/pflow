<?php

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
        catch( ezcBaseTypeException $e)
        {
            $this->assertTrue(true);
        }

        try
        {
            $baseTest = new ezcBaseTest();
            ezcBase::assertType($a, "ezcBase");
            $this->fail("Expected an ezcBaseTypeException from 'ezcBaseTest'");
        }
        catch( ezcBaseTypeException $e)
        {
            $this->assertTrue(true);
        }
    }


    public static function suite()
    {
        return new ezcTestSuite("ezcBaseTest");
    }
}
?>
