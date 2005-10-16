<?php

/**
 * ezcConsoleToolsOutputTest 
 * 
 * @package ConsoleTools
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 */
class ezcConsoleToolsProgressbarTest extends ezcTestCase
{


    // {{{   suite()

	public static function suite()
	{
		return new ezcTestSuite( "ezcConsoleToolsProgressbarTest" );
	}

    // }}}

    // {{{ setUp() 

    /**
     * setUp 
     * 
     * @access public
     * @return 
     */
    public function setUp()
    {
    }

    // }}} 
    // {{{ tearDown()  

    /**
     * tearDown 
     * 
     * @access public
     * @return 
     */
    public function tearDown()
    {
    }

    // }}} 

    public function testProgress()
    {
        echo "\n\n";
        $out = new ezcConsoleOutput();
        $bar = new ezcConsoleProgressbar( $out, array( 'max' => 120, 'step' => 10 )  );
        for ( $i = 0; $i < 13; $i++ )
        {
            $bar->advance();
            echo "\n\n";
        }
    }
    
    // private
    
    // {{{ common

    private function commonTableTest( $refFile, $tableData, $settings, $options, $headrows = array() )
    {
        $table = ezcConsoleTable::create( 
            $tableData,
            $this->output,
            $settings,
            $options
        );
        $table->setCellFormat( 'red', 0, 0 );
        foreach ( $headrows as $row )
        {
            $table->makeHeadRow( $row );
        }
        $this->assertEquals(
            file_get_contents( dirname( __FILE__ ) . '/dat/' . $refFile . '.dat' ),
            implode( "\n", $table->getTable() ),
            'Table not correctly generated for ' . $refFile . '.'
        );
    }

    // }}}
    
}

?>
