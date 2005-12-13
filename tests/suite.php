<?php
/**
 * ezcConsoleToolsSuite
 * 
 * @package ConsoleTools
 * @subpackage Tests
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Require test suite for ConsoleOutput class.
 */
require_once 'output_test.php';
/**
 * Require test suite for ConsoleOutputFormat class.
 */
require_once 'output_format_test.php';
/**
 * Require test suite for ConsoleOutputFormats class.
 */
require_once 'output_formats_test.php';
/**
 * Require test suite for ConsoleOutputOptions class.
 */
require_once 'output_options_test.php';
/**
 * Require test suite for ConsoleParameter class.
 */
require_once 'parameter_test.php';
/**
 * Require test suite for ConsoleTable class.
 */
require_once 'table_test.php';
/**
 * Require test suite for ConsoleProgressbar class.
 */
require_once 'progressbar_test.php';
/**
 * Require test suite for ConsoleStatusbar class.
 */
require_once 'statusbar_test.php';
    
/**
 * Test suite for ConsoleTools package.
 * 
 * @package ConsoleTools
 * @subpackage Tests
 */
class ezcConsoleToolsSuite extends ezcTestSuite
{
	public function __construct()
	{
		parent::__construct();
        $this->setName( "ConsoleTools" );

		$this->addTest( ezcConsoleToolsOutputTest::suite() );
		$this->addTest( ezcConsoleToolsOutputFormatTest::suite() );
		$this->addTest( ezcConsoleToolsOutputFormatsTest::suite() );
		$this->addTest( ezcConsoleToolsOutputOptionsTest::suite() );
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
