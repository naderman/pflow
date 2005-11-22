<?php
/**
 * @package Base
 * @subpackage Tests
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license BSD {@link http://ez.no/licenses/bsd}
 */

require_once( "base_test.php");

/**
 * @package Base
 * @subpackage Tests
 */
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
