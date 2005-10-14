<?php

/**
 * ezcConsoleToolsOutputTest 
 * 
 * @package ConsoleTools
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 */
class ezcConsoleToolsTableTest extends ezcTestCase
{

    private $tableData1 = array( 
        array( 'Heading no. 1', 'Longer heading no. 2', 'Head 3' ),
        array( 'Data cell 1', 'Data cell 2', 'Data cell 3' ),
        array( 'Long long data cell with even more text in it...', 'Data cell 4', 'Data cell 5' ),
        array( 'a b c d e f g h i j k l m n o p q r s t u v w x ', 'Data cell', 'Data cell' ),
    );

    private $tableData2 = array( 
        array( 'a', 'b', 'c', 'd', 'e', 'f' ),
        array( 'g', 'h', 'i', 'j', 'k', 'l' ),
    );

    private $tableData3 = array( 
        array( 'Parameter', 'Shortcut', 'Descrition' ),
        array( 'Append text to a file. This parameter takes a string value and may be used multiple times.', '--append', '-a'  ),
        array( 'Prepend text to a file. This parameter takes a string value and may be used multiple times.', '--prepend', '-p'  ),
        array( 'Forces the action desired without paying attention to any errors.', '--force', '-f' ),
        array( 'Silence all kinds of warnings issued by this program.', '--silent', '-s' ),
    );
    
    private $tableData4 = array( 
        array( 'Some very very long data here.... and it becomes even much much longer... and even longer....', 'Short', 'Some very very long data here.... and it becomes even much much longer... and even longer....', 'Short' ),
        array( 'Short', "Some very very long data here....\n\nand it becomes even much much longer...\n\nand even longer....", 'Short', 'Some very very long data here.... and it becomes even much much longer... and even longer....' ),
    );

    // {{{   suite()

	public static function suite()
	{
		return new ezcTestSuite( "ezcConsoleToolsTableTest" );
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
        $this->output = new ezcConsoleOutput( 
            array( 
                'format' => array(
                    'red' => array( 
                        'color' => 'red',
                        'style' => 'bold'
                    ),
                    'blue' => array( 
                        'color' => 'blue',
                        'style' => 'bold'
                    ),
                    'green' => array( 
                        'color' => 'green',
                        'style' => 'bold'
                    ),
                    'magenta' => array( 
                        'color' => 'magenta',
                        'style' => 'bold'
                    ),
                ),
            )
        );
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

    public function testTable1a()
    {
        $this->commonTableTest(
            __FUNCTION__, 
            $this->tableData1,
            array( 'cols' => count( $this->tableData1[0] ), 'width' => 80 ),
            array( 'lineFormatHead' => 'green' ),
            array( 0 )
        );
    }
    
    public function testTable1b()
    {
        $this->commonTableTest(
            __FUNCTION__, 
            $this->tableData1,
            array( 'cols' => count( $this->tableData1[0] ), 'width' => 40 ),
            array( 'lineFormatHead' => 'red',  ),
            array( 0 )
        );
    }
    
    public function testTable2a()
    {
        $this->commonTableTest(
            __FUNCTION__,
            $this->tableData2,
            array( 'cols' => count( $this->tableData2[0] ), 'width' =>  60 ),
            array( 'lineFormatHead' => 'magenta', 'colAlign' => ezcConsoleTable::ALIGN_RIGHT, 'widthType' => ezcConsoleTable::WIDTH_FIXED )
        );
    }
    
    public function testTable2b()
    {
        $this->commonTableTest(
            __FUNCTION__,
            $this->tableData2,
            array( 'cols' => count( $this->tableData2[0] ), 'width' =>  60 ),
            array( 'lineFormatHead' => 'magenta', 'colAlign' => ezcConsoleTable::ALIGN_RIGHT )
        );
    }
    
    public function testTable3a()
    {
        $this->commonTableTest(
            __FUNCTION__,
            $this->tableData3,
            array( 'cols' => count( $this->tableData3[0] ), 'width' =>  120 ),
            array( 'lineFormatHead' => 'blue', 'colAlign' => ezcConsoleTable::ALIGN_CENTER, 'lineVertical' => '#', 'lineHorizontal' => '#', 'corner' => '#' ),
            array( 0, 3 )
        );
    }
    
    public function testTable3b()
    {
        $this->commonTableTest(
            __FUNCTION__,
            $this->tableData3,
            array( 'cols' => count( $this->tableData3[0] ), 'width' =>  80 ),
            array( 'lineFormatHead' => 'magenta', 'lineVertical' => 'v', 'lineHorizontal' => 'h', 'corner' => 'c' ),
            array( 1, 2 )
        );
    }
     
    public function testTable4a()
    {
        $this->commonTableTest(
            __FUNCTION__,
            $this->tableData4,
            array( 'cols' => count( $this->tableData4[0] ), 'width' =>  120 ),
            array( 'lineFormatHead' => 'blue', 'colAlign' => ezcConsoleTable::ALIGN_CENTER, 'colWrap' => ezcConsoleTable::WRAP_CUT ),
            array( 0 )
        );
    }
    
    public function testTable4b()
    {
        $this->commonTableTest(
            __FUNCTION__,
            $this->tableData4,
            array( 'cols' => count( $this->tableData4[0] ), 'width' =>  120 ),
            array( 'lineFormatHead' => 'blue', 'colAlign' => ezcConsoleTable::ALIGN_LEFT, 'colWrap' => ezcConsoleTable::WRAP_AUTO ),
            array( 0 )
        );
    }
    
    public function testTable4c()
    {
        $this->commonTableTest(
            __FUNCTION__,
            $this->tableData4,
            array( 'cols' => count( $this->tableData4[0] ), 'width' =>  120 ),
            array( 'lineFormatHead' => 'blue', 'colAlign' => ezcConsoleTable::ALIGN_CENTER, 'colWrap' => ezcConsoleTable::WRAP_NONE ),
            array( 0 )
        );
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
