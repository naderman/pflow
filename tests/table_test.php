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

    private $tableData = array( 
        array( 'Heading no. 1', 'Longer heading no. 2', 'Head 3' ),
        array( 'Data cell 1', 'Data cell 2', 'Data cell 3' ),
        array( 'Long long data cell with even more text in it...', 'Data cell 4', 'Data cell 5' ),
        array( 'a b c d e f g h i j k l m n o p q r s t u v w x ', 'Data cell', 'Data cell' ),
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
        $table = new ezcConsoleTable( 
            $this->output,
            array( 'cols' => 3, 'width' =>  80 ),
            array( 'lineFormat' => 'default', 'lineFormatHead' => 'red' )
        );
        foreach ( $this->tableData as $row => $data )
        {
            $row == 0 ? $table->addHeadRow( $data ) : $table->addRow( $data );
        }
        echo "\n\n";
        $table->outputTable();
        echo "\n\n";
    }
    
    public function testTable2()
    {
        $table = new ezcConsoleTable( 
            $this->output,
            array( 'cols' => 3, 'width' =>  60 ),
            array( 'lineFormatHead' => 'magenta', 'align' => ezcConsoleTable::ALIGN_CENTER )
        );
        foreach ( $this->tableData as $row => $data )
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
            array( 'cols' => 3, 'width' =>  120 ),
            array( 'lineFormatHead' => 'blue', 'align' => ezcConsoleTable::ALIGN_RIGHT )
        );
        foreach ( $this->tableData as $row => $data )
        {
            $row == 0 || $row == 2 ? $table->addHeadRow( $data ) : $table->addRow( $data );
        }
        echo "\n\n";
        $table->outputTable();
        echo "\n\n";
    }
}

?>
