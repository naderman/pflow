<?php
/**
 * File containing the ezcConsoleTable class.
 *
 * @package ConsoleTools
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Creating tables to be printed to the console. 
 *
 * <code>
 * 
 * // ... creating ezcConsoleOutput object
 *
 * $options = array(
 *  'lineFormatHead' => 'red',  // Make header rows surrounded by red lines
 * );
 * 
 * $table = new ezcConsoleTable( $out, array('width' => 60, 'cols' = 3), $options );
 * // Generate a header row:
 * $table->addHeadRow( array( 'First col', 'Second col', 'Third col' ) );
 * // Right column will be the largest
 * $table->addRow( array( 'Data', 'Data', 'Very very very very very long data' ) );
 * $table->output();
 *
 * </code>
 * 
 *
 * @see ezcConsoleOutput
 * @package ConsoleTools
 * @version //autogen//
 */
class ezcConsoleTable
{
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

    /**
     * The width given by settings must be used even if the data allows it smaller. 
     */
    const WIDTH_FIXED = 1;
    /**
     * The width given by settings is a maximum value, if data allows it, the table gets smaller.
     */
    const WIDTH_MAX = 2;

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

    /**
     * Options for the table.
     *
     * @var array(string)
     */
    protected $options = array(
        'colWidth'       => 'auto',
        'colWrap'        => ezcConsoleTable::WRAP_AUTO,
        'colAlign'       => ezcConsoleTable::ALIGN_LEFT,
        'colPadding'     => ' ',

        'widthType'      => ezcConsoleTable::WIDTH_MAX,
        
        'lineVertical'   => '-',
        'lineHorizontal' => '|',

        'corner'         => '+',

        'lineFormat'     => 'default',
        'lineFormatHead' => 'default',
    );

    /**
     * The ezcConsoleOutput object to use.
     *
     * @var ezcConsoleOutput
     */
    protected $outputHandler;

    /**
     * The actual data to be represented in the table. 
     * 
     * @var array
     */
    protected $tableData = array();

    /**
     * Mapping for head line rows (keys of the rows).
     * 
     * @var array
     */
    protected $tableHeadRows = array();

    /**
     * Text format mappings for table cell data. 
     * 
     * @var array(int => array(int => string))
     */
    protected $cellFormats = array();

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
    public static function create( $data, ezcConsoleOutput $outHandler, $settings, $options = array() )
    {
        $table = new ezcConsoleTable( $outHandler, $settings, $options );
        foreach ( $data as $row => $cells )
        {
            $table->addRow( $cells );
        }
        return $table;
    }

    /**
     * Set options for the table.
     *
     * @see ezcConsoleTable::$options
     * 
     * @param array $options Options to set.
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
                trigger_error( "Unknown option <{$name}>.", E_USER_WARNING );
            }
        }
    }

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
    public function addRow( $rowData )
    {
        $this->tableData[] = $rowData;
        end($this->tableData);
        return key($this->tableData);
    }

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
    public function addHeadRow( $rowData )
    {
        $this->addRow( $rowData );
        end( $this->tableData );
        $this->tableHeadRows[key( $this->tableData )] = true;
        return key($this->tableData);
    }

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
    public function setCell( $row, $column, $cellData )
    {
        $this->tableData[$row][$column] = $cellData;
    }

    /**
     * Set the text format for a specific cell.
     * This method allows you to set a cell format for a sepcific
     * cell of the table or a complete row (leaving the $col) parameter
     * empty. You can use any format you created in our output handler to
     * format a cell.
     * 
     * @param int $row The row number where the cell to format resides in.
     * @param int $col The column part to identify a cell or null to format a row.
     * @param string $format The format to use for the cell data.
     */
    public function setCellFormat( $format, $row, $col = null  )
    {
        if ( isset( $col ) ) 
        {
            $this->cellFormats[$row][$col] = $format;
        }
        else
        {
            for ( $i = 0; $i < $this->settings['cols']; $i++ )
            {
                $this->cellFormats[$row][$i] = $format;
            }
        }
    }

    /**
     * Make a row to a header row.
     * Defines the row with the specified number to be a header row.
     *
     * @param int $row Number of the row to affect.
     * 
     * @see eczConsoleTable::setDefaultRow()
     */
    public function makeHeadRow( $row )
    {
        $this->tableHeadRows[$row] = true;
    }

    /**
     * Make a row to a default row.
     * Defines the row with the specified number to be a default row.
     * (Used to bring header rows back to normal.)
     *
     * @param int $row Number of the row to affect.
     *
     * @see eczConsoleTable::setHeadRow()
     */
    public function makeDefaultRow( $row )
    {
        if ( isset( $this->tableHeadRows[$row] ) )
        {
            unset( $this->tableHeadRows[$row] );
        }
    }

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

    /**
     * Output the table.
     * Prints the complete table to the console.
     *
     */
    public function outputTable() 
    {
        echo implode( "\n", $this->generateTable() );
    }

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
                $table[] = $this->generateRow( $brkCells, $colWidth, $header, $row );
            }
            $table[] = $this->generateBorder( $colWidth, $header || isset( $this->tableHeadRows[$row + 1] ) );
        }
        return $table; 
    }

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
            $border .= $this->options['corner'] . str_repeat( $this->options['lineVertical'], $width + ( 2 * strlen( $this->options['colPadding'] ) ) );
        }
        $border .= $this->options['corner'];

        return $this->outputHandler->styleText( $border, $this->options[ ( $header ? 'lineFormatHead' : 'lineFormat' ) ] );
    }

    /**
     * Generate a single physical row.
     * This method generates the string for a single physical table row.
     * 
     * @param array $cells    Cells of the row.
     * @param array $colWidth Calculated columns widths.
     * @return string The row.
     */
    private function generateRow( $cells, $colWidth, $header = false, $row )
    {
        $rowData = '';
        for ( $cell = 0; $cell < count( $colWidth ); $cell++ )
        {
            $data = isset( $cells[$cell] ) ? $cells[$cell] : '';
            $rowData .= $this->outputHandler->styleText( 
                            $this->options['lineHorizontal'], 
                            $this->options[$header ? 'lineFormatHead' : 'lineFormat']
                        );
            $rowData .= ' ';
            $rowData .= $this->outputHandler->styleText(
                            str_pad( $data, $colWidth[$cell], ' ', $this->options['colAlign'] ),
                            isset( $this->cellFormats[$row][$cell] ) ? $this->cellFormats[$row][$cell] : null
                        );
            $rowData .= ' ';
        }
        $rowData .= $this->outputHandler->styleText( $this->options['lineHorizontal'], $this->options[$header ? 'lineFormatHead' : 'lineFormat'] );
        return $rowData;
    }

    /**
     * Returns auto broken rows from an array of cells.
     * The data provided by a user may not fit into a cell calculated by the 
     * class. In this case, the data can be automatically wrapped. The table 
     * row then spans over multiple physical console lines.
     * 
     * @param array $cells    Array of cells in one row.
     * @param array $colWidth Columns widths array.
     * @return array Physical rows generated out of this row.
     */
    private function breakRows( $cells, $colWidth ) 
    {
        $rows = array();
        // Iterate through cells of the row
        foreach ( $cells as $cell => $data ) 
        {
            // Physical row id, start with 0 for each row
            $row = 0;
            // Split into multiple physical rows if manual breaks exist
            $dataLines = explode( "\n", $data );
            foreach ( $dataLines as $dataLine ) 
            {
                // Does the physical row fit?
                if ( strlen( $dataLine ) > ( $colWidth[$cell] ) )
                {
                    switch ( $this->options['colWrap'] )
                    {
                        case ezcConsoleTable::WRAP_AUTO:
                            $subLines = explode( "\n", wordwrap( $dataLine, $colWidth[$cell], "\n", true ) );
                            foreach ( $subLines as $lineNo => $line )
                            {
                                $rows[$row++][$cell] = $line;
                            }
                            break;
                        case ezcConsoleTable::WRAP_CUT:
                            $rows[$row++][$cell] = substr( $dataLine, 0, $colWidth[$cell] );
                            break;
                        case ezcConsoleTable::WRAP_NONE:
                        default:
                            $rows[$row++][$cell] = $dataLine;
                            break;
                    }
                }
                else
                {
                    $rows[$row++][$cell] = $dataLine;
                }
            }
        }
        return $rows;
    }

    /**
     * Determine width of each single column. 
     */
    private function getColWidths()
    {
        if ( is_array( $this->options['colWidth'] ) )
        {
            return $this->options['colWidth'];
        }
        // Subtract border and padding chars from global width
        $globalWidth = $this->settings['width'] - ( $this->settings['cols'] * ( 2 * strlen( $this->options['colPadding'] ) + 1 ) ) - 1;
        // Width of a column if each is made equal
        $colNormWidth = round( $globalWidth / $this->settings['cols'] );
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
            elseif ( $this->options['widthType'] === ezcConsoleTable::WIDTH_FIXED )
            {
                $widthSum = array_sum( $colWidth );
                foreach ( $colWidth as $col => $width )
                {
                    $colWidth[$col] += floor( $width / $widthSum * $spareWidth );
                }
            }
        }
        // Finally sanitize values from rounding issues, if necessary
        if ( ( $colSum = array_sum( $colWidth ) ) != $globalWidth && $this->options['widthType'] === ezcConsoleTable::WIDTH_FIXED )
        {
            $colWidth[count( $colWidth ) - 1] -= $colSum - $globalWidth;
        }
        return $colWidth;
    }

    /**
     * Check and set the settings submited to the constructor. 
     * 
     * @param array $settings 
     *
     * @throws ezcBaseConfigException On an invalid setting.
     */
    private function setSettings( $settings )
    {
        if ( !isset( $settings['width'] ) || !is_int( $settings['width'] ) || $settings['width'] < 0 ) 
        {
            throw new ezcBaseConfigException( 'width', ezcBaseConfigException::VALUE_OUT_OF_RANGE, isset( $settings['width'] ) ? $settings['width'] : 'null' );
        }
        if ( !isset( $settings['cols'] ) || !is_int( $settings['cols'] ) || $settings['cols'] < 0 ) 
        {
            throw new ezcBaseConfigException( 'cols', ezcBaseConfigException::VALUE_OUT_OF_RANGE, isset( $settings['cols'] ) ? $settings['cols'] : 'null' );
        }
        $this->settings = $settings;
    }
}
?>
