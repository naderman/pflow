<?php

require_once( "base_test.php");

class ezcBaseSuite extends ezcTestSuite
{
	public function __construct()
	{
		parent::__construct();
        $this->setName("Base");
        
		$this->addTest( ezcBaseTest::suite() );
	}

    public static function suite()
    {
        return new ezcBaseSuite();
    }
}

?>
