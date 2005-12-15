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
 * // Initialize the console output handler
 * $out = new ezcConsoleOutput();
 * // Define a new format "headline"
 * $out->formats->headline->color = 'red';
 * $out->formats->headline->style = array( 'bold' );
 * // Define a new format "sum"
 * $out->formats->sum->color = 'blue';
 * $out->formats->sum->style = array( 'negative' );
 * 
 * // Create a new table
 * $table = new ezcConsoleTable( $out, 60, 1 );
 * 
 * // Create first row and in it the first cell
 * $table[0][0]->content = 'Headline 1';
 * 
 * // Create 3 more cells in row 0
 * for ( $i = 2; $i < 5; $i++ )
 * {
 *      $table[0][]->content = "Headline $i";
 * }
 * 
 * $data = array( 1, 2, 3, 4);
 * 
 * // Create some more data in the table...
 * foreach ( $data as $value )
 * {
 *      // Create a new row each time and set it's contents to the actual value
 *      $table[][0]->content = $value;
 * }
 * 
 * // Set another border format for our headline row
 * $table[0]->borderFormat = 'headline';
 * 
 * // Set the content format for all cells of the 3rd row to "sum"
 * $table[2]->format = 'sum';
 * 
 * $table->outputTable();
 *
 * </code>
 * 
 *
 * @see ezcConsoleOutput
 * @package ConsoleTools
 * @version //autogen//
 */
class ezcConsoleTable implements Countable, Iterator, ArrayAccess
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
     * Align text in the default direction. 
     */
    const ALIGN_DEFAULT = -1;
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
    protected $settings = array( 
        'width' => 100,
        'cols'  => 1,
    );

    /**
     * Options for the table.
     *
     * @var ezcConsoleTableOptions
     */
    protected $options;

    /**
     * The ezcConsoleOutput object to use.
     *
     * @var ezcConsoleOutput
     */
    protected $outputHandler;

    /**
     * Collection of the rows that are contained in the table. 
     * 
     * @var array
     */
    protected $rows;

    /**
     * Creates a new table.
     *
     * @param ezcConsoleOutput $outHandler    Output handler to utilize
     * @param int $width                      Overall width of the table (chars).
     * @param int $cols                       Number of columns in a row.
     * @param ezcConsoleTableOptions $options Options
     *
     * @see ezcConsoleTable::$settings
     * @see ezcConsoleTable::$options
     *
     * @throws ezcBaseConfigException On an invalid setting.
     */
    public function __construct( ezcConsoleOutput $outHandler, $width, $cols, ezcConsoleTableOptions $options = null ) 
    {
        $this->outputHandler = $outHandler;
        $this->__set( 'width', $width );
        $this->__set( 'cols', $cols );
        $this->__set( 'options', isset( $options ) ? $options : new ezcConsoleTableOptions() );
    }

    /**
     * Returns the table in a string.
     * Returns the entire table as an array of printable lines. Each element of
     * the array represents a physical line of the drawn table, including all
     * borders and stuff, so you can simply print the table using
     * <code>
     * echo implode( "\n" , $table->getTable() ):
     * </code>
     * which is basically what {@link ezcConsoleTable::outputTable()} does.
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
     */
    public function outputTable() 
    {
        echo implode( "\n", $this->generateTable() );
    }

    /**
     * Returns if the given offset exists.
     * This method is part of the ArrayAccess interface to allow access to the
     * data of this object as if it was an array.
     * 
     * @param int $offset The offset to check.
     * @return bool True when the offset exists, otherwise false.
     */
    public function offsetExists( $offset )
    {
        if ( !is_int( $offset ) || $offset < 0 )
        {
            throw new ezcBaseTypeException( 'int+', gettype( $offset ) );
        }
        return isset( $this->rows[$offset] );
    }

    // From here only interface method implementations follow, which are not intended for direct usage

    /**
     * Returns the element with the given offset. 
     * This method is part of the ArrayAccess interface to allow access to the
     * data of this object as if it was an array. In case of the
     * ezcConsoleTable class this method always returns a valid row object
     * since it creates them on the fly, if a given item does not exist.
     * 
     * @param int $offset The offset to check.
     * @return object(ezcConsoleTableCell)
     */
    public function offsetGet( $offset )
    {
        if ( !isset( $offset ) )
        {
            $offset = count( $this );
            $this->rows[$offset] = new ezcConsoleTableRow();
        }
        if ( !is_int( $offset ) || $offset < 0 )
        {
            throw new ezcBaseTypeException( 'int+', gettype( $offset ) );
        }
        if ( !isset( $this->rows[$offset] ) )
        {
            $this->rows[$offset] = new ezcConsoleTableRow();
        }
        return $this->rows[$offset];
    }

    /**
     * Set the element with the given offset. 
     * This method is part of the ArrayAccess interface to allow access to the
     * data of this object as if it was an array. 
     * 
     * @param int $offset                 The offset to assign an item to.
     * @param object(ezcConsoleTableCell) The item to assign.
     */
    public function offsetSet( $offset, $value )
    {
        if ( !( $value instanceof ezcConsoleTableCell ) )
        {
            throw new ezcBaseTypeException( 'ezcConsoleTableCell', gettype( $value ) );
        }
        if ( !isset( $offset ) )
        {
            $offset = count( $this );
        }
        if ( !is_int( $offset ) || $offset < 0 )
        {
            throw new ezcBaseTypeException( 'int+', gettype( $offset ) );
        }
        $this->rows[$offset] = $value;
    }

    /**
     * Unset the element with the given offset. 
     * This method is part of the ArrayAccess interface to allow access to the
     * data of this object as if it was an array. 
     * 
     * @param int $offset The offset to unset the value for.
     */
    public function offsetUnset( $offset )
    {
        if ( !is_int( $offset ) || $offset < 0 )
        {
            throw new ezcBaseTypeException( 'int+', gettype( $offset ) );
        }
        if ( isset( $this->rows[$offset] ) )
        {
            unset( $this->rows[$offset] );
        }
    }

    /**
     * Returns the number of cells in the row.
     * This method is part of the Countable interface to allow the usage of
     * PHP's count() function to check how many cells this row has.
     *
     * @return int Number of cells in this row.
     */
    public function count()
    {
        $keys = array_keys( $this->rows );
        return count( $keys ) > 0 ? ( end( $keys ) + 1 ) : 0;
    }

    /**
     * Returns the currently selected cell.
     * This method is part of the Iterator interface to allow acces to the 
     * cells of this row by iterating over it like an array (e.g. using
     * foreach).
     * 
     * @return object(ezcConsoleTableCell) The currently selected cell.
     */
    public function current()
    {
        return current( $this->rows );
    }

    /**
     * Returns the key of the currently selected cell.
     * This method is part of the Iterator interface to allow acces to the 
     * cells of this row by iterating over it like an array (e.g. using
     * foreach).
     * 
     * @return int The key of the currently selected cell.
     */
    public function key()
    {
        return key( $this->rows );
    }

    /**
     * Returns the next cell and selects it or false on the last cell.
     * This method is part of the Iterator interface to allow acces to the 
     * cells of this row by iterating over it like an array (e.g. using
     * foreach).
     *
     * @return mixed ezcConsoleTableCell if the next cell exists, or false.
     */
    public function next()
    {
        return next( $this->rows );
    }

    /**
     * Selects the very first cell and returns it.
     * This method is part of the Iterator interface to allow acces to the 
     * cells of this row by iterating over it like an array (e.g. using
     * foreach).
     *
     * @return ezcConsoleTableCell The very first cell of this row.
     */
    public function rewind()
    {
        return reset( $this->rows );
    }

    /**
     * Returns if the current cell is valid.
     * This method is part of the Iterator interface to allow acces to the 
     * cells of this row by iterating over it like an array (e.g. using
     * foreach).
     *
     * @return ezcConsoleTableCell The very first cell of this row.
     */
    public function valid()
    {
        return current( $this->rows ) !== false;
    }

    /**
     * Property read access.
     * 
     * @param string $key Name of the property.
     * @return mixed Value of the property or null.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If the the desired property is not found.
     */
    public function __get( $key )
    {
        switch ($key) 
        {
            case 'options':
                return $this->$key;
                break;
            case 'width':
            case 'cols':
                return $this->settings[$key];
                break;
            default:
                break;
        }
        throw new ezcBasePropertyNotFoundException( $key );
    }

    /**
     * Property write access.
     * 
     * @param string $key Name of the property.
     * @param mixed $val  The value for the property.
     *
     * @throws ezcBaseConfigException
     *         If a the value for the property options is not an instance of
     *         ezcConsoleOutputOptions
     *         {@link ezcBaseConfigException::VALUE_OUT_OF_RANGE}.
     */
    public function __set( $key, $val )
    {
        switch ($key) 
        {
            case 'options':
                if ( !( $val instanceof ezcConsoleTableOptions ) )
                {
                    throw new ezcBaseTypeException( 'ezcConsoleTableOptions', gettype( $val ) );
                }
                $this->options = $val;
                return;
                break;
            case 'width':
            case 'cols':
                if ( $val < 1 )
                {
                    throw new ezcBaseConfigException( $key, ezcBaseConfigException::VALUE_OUT_OF_RANGE, $val );
                }
                $this->settings[$key] = $val; 
                return;
                break;
            default:
                break;
        }
        throw new ezcBasePropertyNotFoundException( $key );
    }
 
    /**
     * Property isset access.
     * 
     * @param string $key Name of the property.
     * @return bool True is the property is set, otherwise false.
     */
    public function __isset( $key )
    {
        switch ($key) 
        {
            case 'options':
            case 'width':
            case 'cols':
                return true;
                break;
            default:
                break;
        }
        return false;
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
        $table[] = $this->generateBorder( $colWidth, $this[0]->borderFormat );
        // Rows submitted by the user
        for ( $i = 0;  $i < count( $this->rows ); $i++ )
        {
            // Auto broken rows
            foreach ( $this->breakRows( $this->rows[$i], $colWidth ) as $brkRow => $brkCells )
            {
                $table[] = $this->generateRow( $brkCells, $colWidth, $this->rows[$i] );
            }
            $afterBorderFormat = isset( $this->rows[$i + 1] ) && $this->rows[$i + 1]->borderFormat != 'default' ? $this->rows[$i + 1]->borderFormat : $this->rows[$i]->borderFormat;
            $table[] = $this->generateBorder( $colWidth, $afterBorderFormat );
        }
        return $table; 
    }

    /**
     * Generate top/bottom borders of rows. 
     * 
     * @param array $colWidth Array of column width.
     * @return string The Border string.
     */
    private function generateBorder( $colWidth, $format )
    {
        $border = '';
        foreach ( $colWidth as $col => $width )
        {
            $border .= $this->options->corner . str_repeat( $this->options->lineVertical, $width + ( 2 * strlen( $this->options->colPadding ) ) );
        }
        $border .= $this->options->corner;

        return $this->outputHandler->formatText( $border, $format );
    }

    /**
     * Generate a single physical row.
     * This method generates the string for a single physical table row.
     * 
     * @param array $cells    Cells of the row.
     * @param array $colWidth Calculated columns widths.
     * @return string The row.
     */
    private function generateRow( $cells, $colWidth, $row )
    {
        $rowData = '';
        for ( $cell = 0; $cell < count( $colWidth ); $cell++ )
        {
            $align = $this->determineAlign( $row, $cell );
            $format = $this->determineFormat( $row, $cell );
            $borderFormat = $this->determineBorderFormat( $row );
            
            $data = isset( $cells[$cell] ) ? $cells[$cell] : '';
            $rowData .= $this->outputHandler->formatText( 
                            $this->options->lineHorizontal, 
                            $row->borderFormat
                        );
            $rowData .= ' ';
            $rowData .= $this->outputHandler->formatText(
                            str_pad( $data, $colWidth[$cell], ' ', $align ),
                            $row[$cell]->format
                        );
            $rowData .= ' ';
        }
        $rowData .= $this->outputHandler->formatText( $this->options->lineHorizontal, $row->borderFormat );
        return $rowData;
    }

    /**
     * Determine the alignement of a cell.
     * Walks the inheritance path upwards to determine the alignement of a 
     * cell. Checks first, if the cell has it's own alignement (apart from 
     * ezcConsoleTable::ALIGN_DEFAULT). If not, checks the row for an 
     * alignement setting and uses the default alignement if not found.
     * 
     * @param ezcConsoleTableRow $row   The row this cell belongs to.
     * @param ezcConsoleTableCell $cell Index of the desired cell.
     * @return int An alignement constant (ezcConsoleTable::ALIGN_*).
     */
    private function determineAlign( $row, $cellId = 0 )
    {
        return $row[$cellId]->align !== ezcConsoleTable::ALIGN_DEFAULT 
            ? $row[$cellId]->align
            : $row->align !== ezcConsoleTable::ALIGN_DEFAULT
                ? $row->align
                : $this->options->defaultAlign !== ezcConsoleTable::ALIGN_DEFAULT
                    ? $this->options->defaultAlign
                    : ezcConsoleTable::ALIGN_LEFT;
    }

    /**
     * Determine the format of a cells content.
     * Walks the inheritance path upwards to determine the format of a 
     * cells content. Checks first, if the cell has it's own format (apart 
     * from 'default'). If not, checks the row for a format setting and 
     * uses the default format if not found.
     * 
     * @param ezcConsoleTableRow $row   The row this cell belongs to.
     * @param ezcConsoleTableCell $cell Index of the desired cell.
     * @return string A format name.
     */
    private function determineFormat( $row, $cellId )
    {
        return $row[$cellId]->format !== 'default'
            ? $row[$cellId]->format
            : $row->format !== 'default'
                ? $row->format
                : $this->options->defaultFormat;
    }

    /**
     * Determine the format of a rows border.
     * Walks the inheritance path upwards to determine the format of a 
     * rows border. Checks first, if the row has it's own format (apart 
     * from 'default'). If not, uses the default format.
     * 
     * @param ezcConsoleTableRow $row   The row this cell belongs to.
     * @return string A format name.
     */
    private function determineBorderFormat( $row )
    {
        return $row->borderFormat !== 'default'
            ? $row->borderFormat
            : $this->options->defaultBorderFormat;
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
        foreach ( $colWidth as $cell => $width ) 
        {
            $data = $cells[$cell]->content;
            // Physical row id, start with 0 for each row
            $row = 0;
            // Split into multiple physical rows if manual breaks exist
            $dataLines = explode( "\n", $data );
            foreach ( $dataLines as $dataLine ) 
            {
                // Does the physical row fit?
                if ( strlen( $dataLine ) > ( $colWidth[$cell] ) )
                {
                    switch ( $this->options->colWrap )
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
        if ( is_array( $this->options->colWidth ) )
        {
            return $this->options->colWidth;
        }
        // Subtract border and padding chars from global width
        $globalWidth = $this->width - ( $this->cols * ( 2 * strlen( $this->options->colPadding ) + 1 ) ) - 1;
        // Width of a column if each is made equal
        $colNormWidth = round( $globalWidth / $this->cols );
        $colMaxWidth = array();
        // Determine the longest data for each column
        foreach ( $this->rows as $row => $cells )
        {
            foreach ( $cells as $col => $cell )
            {
                $colMaxWidth[$col] = isset( $colMaxWidth[$col] ) ? max( $colMaxWidth[$col], strlen( $cell->content ) ) : strlen( $cell->content );
            }
        }
        $colWidth = array();
        $colWidthOverflow = array();
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
                $colWidthOverflow[$col] = $maxWidth - $colWidth[$col];
            }
        }
        // Do we have spare to give to the columns again?
        if ( $spareWidth > 0 )
        {
            // Second processing step
            if ( count( $colWidthOverflow ) > 0  )
            {
                $overflowSum = array_sum( $colWidthOverflow );
                foreach ( $colWidthOverflow as $col => $overflow );
                {
                    $colWidth[$col] += floor( $overflow / $overflowSum * $spareWidth );
                }
            }
            elseif ( $this->options->widthType === ezcConsoleTable::WIDTH_FIXED )
            {
                $widthSum = array_sum( $colWidth );
                foreach ( $colWidth as $col => $width )
                {
                    $colWidth[$col] += floor( $width / $widthSum * $spareWidth );
                }
            }
        }
        // Finally sanitize values from rounding issues, if necessary
        if ( ( $colSum = array_sum( $colWidth ) ) != $globalWidth && $this->options->widthType === ezcConsoleTable::WIDTH_FIXED )
        {
            $colWidth[count( $colWidth ) - 1] -= $colSum - $globalWidth;
        }
        return $colWidth;
    }
}
?>
