<?php
/**
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package Reflection
 * @subpackage Tests
 */

class ezcReflectionClassExternalTest extends ezcReflectionClassTest
{
    /**
     * @var ezcReflectionClass
     */
    protected $class;

    public function setUp()
    {
        $this->class = new ezcReflectionClass( new ReflectionClass( 'ezcReflectionClass' ) );
    }

    public function tearDown()
    {
        unset($this->class);
    }

    public static function suite()
    {
         return new PHPUnit_Framework_TestSuite( "ezcReflectionClassExternalTest" );
    }
}
?>
