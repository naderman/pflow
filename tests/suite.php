<?php

require_once 'output_test.php';
require_once 'parameter_test.php';
require_once 'table_test.php';
    
class ezcConsoleToolsSuite extends ezcTestSuite
{
	public function __construct()
	{
		parent::__construct();
        $this->setName( "ConsoleTools" );

		$this->addTest( ezcConsoleToolsOutputTest::suite() );
		$this->addTest( ezcConsoleToolsParameterTest::suite() );
		$this->addTest( ezcConsoleToolsTableTest::suite() );
	}

    public static function suite()
    {
        return new ezcConsoleToolsSuite( "ezcConsoleToolsSuite" );
    }
}

?>
