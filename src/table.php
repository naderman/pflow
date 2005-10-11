<?php
/**
 * File containing the ezcConsoleTable class.
 *
 * @package ConsoleTools
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 */

/**
 * Creating tables to be printed to the console. 
 *
 * <code>
 * 
 * // ... creating ezcConsoleOutput object
 *
 * $options = array(
 *  'lineColorHead' => 'red',  // Make header rows surrounded by red lines
 * );
 * 
 * $table = new ezcConsoleTable($out, array('width' => 60, 'cols' = 3), $options);
 * // Generate a header row:
 * $table->addRowHead(array('First col', 'Second col', 'Third col'));
 * // Right column will be the largest
 * $table->addRow(array('Data', 'Data', 'Very very very very very long data'));
 * $table->output();
 *
 * </code>
 * 
 *
 * @see ezcConsoleOutput
 * @package ConsoleTools
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 */
class ezcConsoleTable
{
    // {{{ Constants

    /**
     * Automatically wrap text to fit into a column.
     * @see ezcConsoleTable::$options
     */
    const WRAP_AUTO = 1;
    /**
     * Do not wrap text. Columns will be extended to fit the largest text.
     * ATTENTION: This is riscy!
     * @see ezcConsoleTable::$options
     */
    const WRAP_NONE = 2;
    /**
     * Text will be cut to fit into a column.
     * @see ezcConsoleTable::$options
     */
    const WRAP_CUT  = 3;
    
    
    /**
     * Align text in cells to the right.
     */
    const ALIGN_LEFT   = STR_PAD_RIGHT;
    /**
     * Align text in cells to the left.
     */
    const ALIGN_RIGHT  = STR_PAD_LEFT;
    /**
     * Align text in cells to the center.
     */
    const ALIGN_CENTER = STR_PAD_BOTH;

    // }}}
    
    // {{{ $settings

    /**
     * Settings for the table.
     *
     * <code>
     * array(
     *  'width' => <int>,       // Width of the table
     *  'cols'  => <int>,       // Number of columns
     * );
     * </code>
     *
     * @var array(string)
     */
    protected $settings;

    // }}}

    // {{{ $options

    /**
     * Options for the table.
     *
     * @var array(string)
     */
    protected $options = array(
        'colWidth'  => 'auto',      // Automatically define column width. Else array of width
                                    // per column like array( 0 => 10, 1 => 15, 2 => 5,...);
        'colWrap'   => ezcConsoleTable::WRAP_AUTO,

        'align'     => ezcConsoleTable::ALIGN_LEFT,
        'padding'   => ' ',         // Padding between cell borders and text
        
        'lineVertical'   => '-',
        'lineHorizontal' => '|',

        'corner'         => '+',

        'lineFormat'     => 'default',
        'lineFormatHead' => 'default',
    );

    // }}}

    // {{{ $optionsOverride

    /**
     * Option sets which locally overwrite the global options for a specific row.
     * 
     * @var array
     */
    protected $optionsOverride = array();

    // }}}

    // {{{ $outputHandler

    /**
     * The ezcConsoleOutput object to use.
     *
     * @var ezcConsoleOutput
     */
    protected $outputHandler;

    // }}}

    // {{{ $tableData

    /**
     * The actual data to be represented in the table. 
     * 
     * @var array
     */
    protected $tableData = array();

    // }}}

    // {{{ $tableHeadRows

    /**
     * Mapping for head line rows (keys of the rows).
     * 
     * @var array
     */
    protected $tableHeadRows = array();

    // }}}

    // {{{ __construct()

    /**
     * Creates a new table.
     *
     * @param ezcConsoleOutput $outHandler Output handler to utilize
     * @param array(string) $settings      Settings
     * @param array(string) $options       Options
     *
     * @see ezcConsoleTable::$settings
     * @see ezcConsoleTable::$options
     *
     * @throws ezcBaseConfigException On an invalid setting.
     */
    public function __construct( ezcConsoleOutput $outHandler, $settings, $options = array() ) 
    {
        $this->outputHandler = $outHandler;
        $this->setSettings( $settings );
        $this->setOptions( $options );
    }

    // }}}
    // {{{ create()

    /**
     * Create an entire table.
     * Creates an entire table from an array of data.
     *
     * <code>
     * array(
     *  0 => array( 0 => <string>, 1 => <string>, 2 => <string>,... ),
     *  1 => array( 0 => <string>, 1 => <string>, 2 => <string>,... ),
     *  2 => array( 0 => <string>, 1 => <string>, 2 => <string>,... ),
     *  ...
     * );
     * </code>
     *
     * @see ezcConsoleTable::__construct()
     * @see ezcConsoleTable::$settings
     * @see ezcConsoleTable::$options
     * 
     * @param array(int -> string) $data   Data for the table
     * @param ezcConsoleOutput $outHandler Output handler to utilize
     * @param array(string) $settings      Settings
     * @param array(string) $options       Options
     */
    public static function create( $data, ezcConsoleOutput $outHandler, $settings, $options = array() ) {
        $table = new ezcConsoleTable( $outHandler, $settings, $options );
        foreach ( $data as $row => $cells )
        {
            $table->addRow( $data );
        }
        return $table;
    }

    // }}}

    // Methods

    // {{{ setOptions()

    /**
     * Set options for the table.
     *
     * @see ezcConsoleTable::$options
     * 
     * @param array $options Options to set.
     * @return void
     */
    public function setOptions( $options )
    {
        foreach ( $options as $name => $val ) 
        {
            if ( isset( $this->options[$name] ) ) 
            {
                $this->options[$name] = $val;
            } 
            else 
            {
                trigger_error( 'Unknowen option "' . $name  . '".', E_USER_WARNING );
            }
        }
    }

    // }}}

    // {{{ addRow()

    /**
     * Add a row of data to the table.
     * Add a row of data to the table. A row looks like this:
     * 
     * <code>
     * array(
     *  0 => <string>, 1 => <string>,...
     * );
     * </code>
     *
     * The options parameter overrides the globally set options.
     * 
     * @param array(int => string) $rowData The data for the row
     * @return int Number of the row.
     */
    public function addRow( $rowData ) {
        $this->tableData[] = $rowData;
        if ( isset( $options ) )
        {
            end( $this->tableData );
            $this->optionsOverride[key( $this->tableData )] = $options;
        }
    }

    // }}}
    // {{{ addHeadRow()

    /**
     * Add a header row to the table.
     * Add a header row to the table. Format {@link ezcConsoleTable::addRow()}.
     *
     * The options parameter overrides the globally set options.
     *
     * @param array(int => string) $rowData The row data
     * @param array(string) $options        Override {@link eczConsoleTable::$options}
     * @return int Number of the row.
     */
    public function addHeadRow( $rowData ) {
        $this->addRow( $rowData );
        end( $this->tableData );
        $this->tableHeadRows[key( $this->tableData )] = true;
    }

    // }}}
    
    // {{{ setCell()

    /**
     * Set data for specific cell.
     * Sets the data for a specific cell. If the row referenced
     * does not exist yet, it's created with empty values. If
     * previous rows do not exist, they are created with empty 
     * values. Existing cell data is overwriten.
     *
     * @param int $row         Row number.
     * @param int $column      Column number.
     * @param string $cellData Data for the cell.
     */ 
    public function setCell( $row, $column, $cellData ) {
        $this->tableData[$row][$column] = $cellData;
    }

    // }}}

    // {{{ makeHeadRow()

    /**
     * Make a row to a header row.
     * Defines the row with the specified number to be a header row.
     *
     * @param int $row Number of the row to affect.
     * 
     * @see eczConsoleTable::setDefaultRow()
     */
    public function makeHeadRow( $row ) {
        $this->tableHeadRows[$row] = true;
    }

    // }}}
    // {{{ makeDefaultRow()

    /**
     * Make a row to a default row.
     * Defines the row with the specified number to be a default row.
     * (Used to bring header rows back to normal.)
     *
     * @param int $row Number of the row to affect.
     *
     * @see eczConsoleTable::setHeadRow()
     */
    public function makeDefaultRow( $row ) {
        if ( isset( $this->tableHeadRows[$row] ) )
        {
            unset( $this->tableHeadRows[$row] );
        }
    }

    // }}}

    // {{{ getTable()

    /**
     * Returns the table in a string.
     * Returns the entire table as an array of printable lines.
     *
     * @return array An array representation of the table.
     */
    public function getTable()
    {
        return $this->generateTable();
    }

    // }}}
    // {{{ outputTable()

    /**
     * Output the table.
     * Prints the complete table to the console.
     *
     * @return void
     */
    public function outputTable() 
    {
        echo implode( "\n", $this->generateTable() );
    }

    // }}}

    // Private

    // {{{ generateTable()

    /**
     * Generate the complete table as an array. 
     * 
     * @return array The table.
     */
    private function generateTable()
    {
        $colWidth = $this->getColWidths();
        $table = array();
        $table[] = $this->generateBorder( $colWidth, ( isset( $this->tableHeadRows[0] ) ) );
        // Rows submitted by the user
        foreach ( $this->tableData as $row => $cells )
        {
            $header = isset( $this->tableHeadRows[$row] );
            // Auto broken rows
            foreach ( $this->breakRows( $cells, $colWidth ) as $brkRow => $brkCells )
            {
                $table[] = $this->generateRow( $brkCells, $colWidth, $header );
            }
            $table[] = $this->generateBorder( $colWidth, $header || isset( $this->tableHeadRows[$row + 1] ) );
        }
        return $table; 
    }

    // }}}
    // {{{ generateBorder()

    /**
     * Generate top/bottom borders of rows. 
     * 
     * @param array $colWidth Array of column width.
     * @return string The Border string.
     */
    private function generateBorder( $colWidth, $header = false )
    {
        $border = '';
        foreach ( $colWidth as $col => $width )
        {
            $border .= $this->options['corner'] . str_repeat( $this->options['lineVertical'], $width + 2 );
        }
        $border .= $this->options['corner'];

        return $this->outputHandler->styleText( $border, $this->options[ ( $header ? 'lineFormatHead' : 'lineFormat' ) ] );
        
    }

    // }}}
    // {{{ generateRow()

    /**
     * Generate a single physical row.
     * This method generates the string for a single physical table row.
     * 
     * @param array $cells    Cells of the row.
     * @param array $colWidth Calculated columns widths.
     * @return string The row.
     */
    private function generateRow( $cells, $colWidth, $header = false )
    {
        $rowData = '';
        for ( $cell = 0; $cell < count( $colWidth ); $cell++ )
        {
            $data = isset( $cells[$cell] ) ? $cells[$cell] : '';
            $rowData .= $this->outputHandler->styleText( 
                            $this->options['lineHorizontal'], 
                            $this->options[ ( $header ? 'lineFormatHead' : 'lineFormat' ) ] 
                     ) 
                     . ' ' . str_pad( $data, $colWidth[$cell], ' ', $this->options['align'] ) 
                     . ' ' ;
        }
        $rowData .= $this->outputHandler->styleText( $this->options['lineHorizontal'], $this->options[ ( $header ? 'lineFormatHead' : 'lineFormat' ) ] );
        return $rowData;
    }

    // }}}

    // {{{ breakRows()

    /**
     * Returns auto broken rows from an array of cells.
     * The data provided by a user may not fit into a cell calculated by the 
     * class. In this case, the data can be automatically wrapped. The table 
     * row then spans over multiple physical console lines.
     * 
     * @param array $cells    Array of cells in one row.
     * @param array $colWidth Columns widths array.
     * @return array Physical rows generated out of this row.
     * @todo Switch to padding option!
     */
    private function breakRows( $cells, $colWidth ) 
    {
        $rows = array();
        foreach ( $cells as $cell => $data ) 
        {
            if ( strlen( $data ) > ( $colWidth[$cell] - 3 ) )
            {
                $data = explode( "\n", wordwrap( $data, $colWidth[$cell], "\n", true ) );
                foreach ( $data as $lineNo => $line )
                {
                    $rows[$lineNo][$cell] = $line;
                }
                
            }
            else
            {
                $rows[0][$cell] = $data;
            }
        }
        return $rows;
    }

    // }}}
    // {{{ getColWidth()

    /**
     * Determine width of each single column. 
     * 
     * @return void
     */
    private function getColWidths()
    {
        if ( is_array( $this->options['colWidth'] ) )
        {
            return $this->options['colWidth'];
        }
        // Width of a column if each is made equal
        $colNormWidth = round($this->settings['width'] / $this->settings['cols']);
        $colMaxWidth = array();
        // Determine the longest data for each column
        foreach ( $this->tableData as $row => $cells )
        {
            foreach ( $cells as $col => $cell )
            {
                $colMaxWidth[$col] = isset( $colMaxWidth[$col] ) ? max( $colMaxWidth[$col], strlen( $cell ) ) : strlen( $cell );
            }
        }
        $colWidth = array();
        $colWidthOverlow = array();
        $spareWidth = 0;
        // Make columns best fit
        foreach ( $colMaxWidth as $col => $maxWidth )
        {
            // Does the largest data of the column fit into the average size 
            // + what we have in spare from earlier columns?
            if ( $maxWidth <= ( $colNormWidth + $spareWidth ) ) 
            {
                // We fit in, make the column as large as necessary
                $colWidth[$col] = $maxWidth;
                $spareWidth += ( $colNormWidth - $maxWidth );
            }
            else
            {
                // Does not fit, use maximal possible width
                $colWidth[$col]  = $colNormWidth + $spareWidth;
                $spareWidth = 0;
                // Store overflow for second processing step
                $colWidthOverlow[$col] = $maxWidth - $colWidth[$col];
            }
        }
        // Do we have spare to give to the columns again?
        if ( $spareWidth > 0 )
        {
            // Second processing step
            if ( count( $colWidthOverlow ) > 0  )
            {
                $overflowSum = array_sum( $colWidthOverlow );
                foreach ( $colWidthOverlow as $col => $overflow )
                {
                    $colWidth[$col] += floor( $overflow / $overflowSum * $spareWidth );
                }
            }
            else
            {
                $widthSum = array_sum( $colWidth );
                foreach ( $colWidth as $col => $width )
                {
                    $colWidth[$col] += floor( $width / $widthSum * $spareWidth );
                }
            }
        }
        // Finally sanitize values from rounding issues, if necessary
        if ( ( $colSum = array_sum( $colWidth ) ) != $this->settings['width'] )
        {
            $colWidth[count( $colWidth ) - 1] -= $colSum - $this->settings['width'];
        }
        return $colWidth;
    }

    // }}}

    // {{{ setSettings()

    /**
     * Check and set the settings submited to the constructor. 
     * 
     * @param array $settings 
     * @return void
     *
     * @throws ezcBaseConfigException On an invalid setting.
     */
    private function setSettings( $settings )
    {
        if ( !isset( $settings['width'] ) || !is_int( $settings['width'] ) || $settings['width'] < 0 ) 
        {
            throw new ezcBaseConfigException( 'Missing or invalid width setting.' );
        }
        if ( !isset( $settings['cols'] ) || !is_int( $settings['cols'] ) || $settings['cols'] < 0 ) 
        {
            throw new ezcBaseConfigException( 'Missing or invalid cols setting.' );
        }
        $this->settings = $settings;
    }

    // }}}

}

?>
