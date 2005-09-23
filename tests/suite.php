<?php

require_once( "output_test.php");
    
class ezcConsoleToolsSuite extends ezcTestSuite
{
	public function __construct()
	{
		parent::__construct("ConsoleTools");
		$this->addTest( ezcConsoleToolsOutputTest::suite() );
	}

    public static function suite()
    {
        return new ezcCacheSuite("ezcConsoleToolsSuite");
    }
}

/*
if( !defined("RUNNER") )
{
    $s = TestUnitSuite::suite();
    $s->run();
}
*/

?>
