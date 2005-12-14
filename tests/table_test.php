<?php
/**
 * ezcConsoleToolsOutputTest 
 * 
 * @package ConsoleTools
 * @subpackage Tests
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Test suite for ezcConsoleTable class.
 * 
 * @package ConsoleTools
 * @subpackage Tests
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
        array( 'Append text to a file. This parameter takes a string value and may be used multiple times.', '--append', '-a' ),
        array( 'Prepend text to a file. This parameter takes a string value and may be used multiple times.', '--prepend', '-p' ),
        array( 'Forces the action desired without paying attention to any errors.', '--force', '-f' ),
        array( 'Silence all kinds of warnings issued by this program.', '--silent', '-s' ),
    );
    
    private $tableData4 = array( 
        array( 'Some very very long data here.... and it becomes even much much longer... and even longer....', 'Short', 'Some very very long data here.... and it becomes even much much longer... and even longer....', 'Short' ),
        array( 'Short', "Some very very long data here....\n\nand it becomes even much much longer...\n\nand even longer....", 'Short', 'Some very very long data here.... and it becomes even much much longer... and even longer....' ),
    );

	public static function suite()
	{
		return new ezcTestSuite( "ezcConsoleToolsTableTest" );
	}

    /**
     * setUp 
     * 
     * @access public
     */
    public function setUp()
    {
        $this->output = new ezcConsoleOutput();
        $formats = array(
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
        );
        foreach ( $formats as $name => $format )
        {
            foreach ( $format as $type => $val )
            {
                $this->output->formats->$name->$type = $val;
            }
        }
    }

    /**
     * tearDown 
     * 
     * @access public
     */
    public function tearDown()
    {
    }

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

    public function testTableManual1 ()
    {
        $table = new ezcConsoleTable( $this->output, count( $this->tableData1[0] ), 100 );
        $i = 0;
        foreach ( $this->tableData1 as $set ) 
        {
            $res = $table->addRow($set);
            $this->assertEquals( 
                $i++,
                $res,
                'Table returned invalid index on adding row.'
            );
        }
    }
    
    public function testTableManual2 ()
    {
        $table = new ezcConsoleTable( $this->output, count( $this->tableData2[0] ), 100 );
        $i = 0;
        foreach ( $this->tableData2 as $set ) 
        {
            $res = $table->addHeadRow($set);
            $this->assertEquals( 
                $i++,
                $res,
                'Table returned invalid index on adding head row.'
            );
        }
    }
    
    public function testTableManual3 ()
    {
        $table = new ezcConsoleTable( $this->output, count( $this->tableData3[0] ), 100 );
        $i = 0;
        foreach ( $this->tableData3 as $set ) 
        {
            if ( $i % 2 == 0 )
            {
                $res = $table->addHeadRow($set);
            }
            else
            {
                $res = $table->addRow($set);
            }
            $this->assertEquals( 
                $i++,
                $res,
                'Table returned invalid index on adding mixed rows.'
            );
        }
    }

    public function testTableConfigurationFailure1 ()
    {
        // Missing 'cols' setting
        try
        {
            $table = new ezcConsoleTable( $this->output, null, 100 );
        }
        catch (ezcBaseConfigException $e)
        {
            $this->assertEquals( 
                ezcBaseConfigException::VALUE_OUT_OF_RANGE,
                $e->getCode(),
                'Wrong exception code thrown on missing <cols> setting.'
            );
            return;
        }
        $this->fail( 'No exception thrown on missing <cols> setting.' );
    }
    
    public function testTableConfigurationFailure2 ()
    {
        // 'cols' setting wrong type
        try
        {
            $table = new ezcConsoleTable( $this->output, 'test', 100 );
        }
        catch (ezcBaseConfigException $e)
        {
            $this->assertEquals( 
                ezcBaseConfigException::VALUE_OUT_OF_RANGE,
                $e->getCode(),
                'Wrong exception code thrown on wrong type for <cols> setting.'
            );
            return;
        }
        $this->fail( 'No exception thrown on wrong type for <cols> setting.' );
    }

    public function testTableConfigurationFailure3 ()
    {
        // 'cols' setting out of range
        try
        {
            $table = new ezcConsoleTable( $this->output, -10, 100 );
        }
        catch (ezcBaseConfigException $e)
        {
            $this->assertEquals( 
                ezcBaseConfigException::VALUE_OUT_OF_RANGE,
                $e->getCode(),
                'Wrong exception code thrown on invalid value of <cols> setting.'
            );
            return;
        }
        $this->fail( 'No exception thrown on invalid value of <cols> setting.' );
    }
    
    public function testTableConfigurationFailure5 ()
    {
        // 'width' setting wrong type
        try
        {
            $table = new ezcConsoleTable( $this->output, 10, false );
        }
        catch (ezcBaseConfigException $e)
        {
            $this->assertEquals( 
                ezcBaseConfigException::VALUE_OUT_OF_RANGE,
                $e->getCode(),
                'Wrong exception code thrown on wrong type for <width> setting.'
            );
            return;
        }
        $this->fail( 'No exception thrown on wrong type for <width> setting.' );
    }

    public function testTableConfigurationFailure6 ()
    {
        // 'width' setting out of range
        try
        {
            $table = new ezcConsoleTable( $this->output, 10, -10 );
        }
        catch (ezcBaseConfigException $e)
        {
            $this->assertEquals( 
                ezcBaseConfigException::VALUE_OUT_OF_RANGE,
                $e->getCode(),
                'Wrong exception code thrown on invalid value of <width> setting.'
            );
            return;
        }
        $this->fail( 'No exception thrown on invalid value of <width> setting.' );
    }
    
    private function commonTableTest( $refFile, $tableData, $settings, $options, $headrows = array() )
    {
        $table = ezcConsoleTable::create( 
            $tableData,
            $this->output,
            $settings['width'],
            $settings['cols']
        );
        foreach ( $options as $key => $val )
        {
            $table->options->$key = $val;
        }
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
        // To prepare test files use this:
        // file_put_contents( dirname( __FILE__ ) . '/dat/' . $refFile . '.dat', implode( "\n", $table->getTable() ) );
    }
}
?>
