<?php

require_once( "output_test.php");
    
class ezcConsoleToolsSuite extends ezcTestSuite
{
	public function __construct()
	{
		parent::__construct();
        $this->setName( "ConsoleTools" );

		$this->addTest( ezcConsoleToolsOutputTest::suite() );
	}

    public static function suite()
    {
        return new ezcConsoleToolsSuite( "ezcConsoleToolsSuite" );
    }
}

?>
