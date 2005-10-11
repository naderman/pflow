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
        array( 'Short', 'Some very very long data here.... and it becomes even much much longer... and even longer....', 'Short', 'Some very very long data here.... and it becomes even much much longer... and even longer....' ),
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


    public function testTable1()
    {
        $this->commonTableTest( 
            $this->tableData1,
            array( 'cols' => 3, 'width' => 80 ),
            array( 'lineFormatHead' => 'red' )
        );
    }

    private function commonTableTest( $tableData, $settings, $options, $headrows = array( 0 ) )
    {
        $table = ezcConsoleTable::create( 
            $tableData,
            $this->output,
            $settings,
            $options
        );
        foreach ( $headrows as $row )
        {
            $table->makeHeadRow( $row );
        }
        echo "\n\n";
        $table->outputTable();
    }
    
    public function testTable2()
    {
        $table = new ezcConsoleTable( 
            $this->output,
            array( 'cols' => count( $this->tableData2[0] ), 'width' =>  60 ),
            array( 'lineFormatHead' => 'magenta', 'colAlign' => ezcConsoleTable::ALIGN_RIGHT )
        );
        foreach ( $this->tableData2 as $row => $data )
        {
            $row == 1 ? $table->addHeadRow( $data ) : $table->addRow( $data );
        }
        echo "\n\n";
        $table->outputTable();
        echo "\n\n";
    }
    
    public function testTable3()
    {
        $table = new ezcConsoleTable( 
            $this->output,
            array( 'cols' => count( $this->tableData3[0] ), 'width' =>  120 ),
            array( 'lineFormatHead' => 'blue', 'colAlign' => ezcConsoleTable::ALIGN_CENTER )
        );
        foreach ( $this->tableData3 as $row => $data )
        {
            $row == 0 ? $table->addHeadRow( $data ) : $table->addRow( $data );
        }
        echo "\n\n";
        $table->outputTable();
        echo "\n\n";
    }
    
    public function testTable4()
    {
        $table = new ezcConsoleTable( 
            $this->output,
            array( 'cols' => count( $this->tableData4[0] ), 'width' =>  120 ),
            array( 'lineFormatHead' => 'blue', 'colAlign' => ezcConsoleTable::ALIGN_CENTER, 'colWrap' => ezcConsoleTable::WRAP_CUT )
        );
        foreach ( $this->tableData4 as $row => $data )
        {
            $row == 0 ? $table->addHeadRow( $data ) : $table->addRow( $data );
        }
        echo "\n\n";
        $table->outputTable();
        echo "\n\n";
    }
    
}

?>
