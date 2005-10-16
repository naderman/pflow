<?php

require_once 'output_test.php';
require_once 'parameter_test.php';
require_once 'table_test.php';
require_once 'progressbar_test.php';
require_once 'statusbar_test.php';
    
class ezcConsoleToolsSuite extends ezcTestSuite
{
	public function __construct()
	{
		parent::__construct();
        $this->setName( "ConsoleTools" );

		$this->addTest( ezcConsoleToolsOutputTest::suite() );
		$this->addTest( ezcConsoleToolsParameterTest::suite() );
		$this->addTest( ezcConsoleToolsTableTest::suite() );
		$this->addTest( ezcConsoleToolsProgressbarTest::suite() );
		$this->addTest( ezcConsoleToolsStatusbarTest::suite() );
	}

    public static function suite()
    {
        return new ezcConsoleToolsSuite( "ezcConsoleToolsSuite" );
    }
}

?>
