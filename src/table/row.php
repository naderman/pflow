<?php
/**
 * File containing the ezcConsoleTableRow class.
 *
 * @package ConsoleTools
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Structure representing a table row in ezcConsoleTable.
 * This class represents a row in an object. You can access
 * the properties of the row directly, but also access the cells of 
 * the row directly as an array (index 0..).
 *
 * <code>
 * $row = new ezcConsoleTableRow();
 * 
 * // Set format of the row lines
 * $row->format = 'headline';
 * 
 * // On the fly create the cell 0:0
 * $row[0]->content = 'Name';
 * // On the fly create the cell 0:1
 * $row[1]->content = 'Cellphone';
 *
 * // Change a setting on a cell
 * $row[0]->align = ezcConsoleTable::ALIGN_CENTER;
 * 
 * // Traverse through the row.
 * foreach ($row as $bar)
 * {
 *     var_dump($bar);
 * }
 * </code>
 *
 * @TODO format -> borderFormat (->format should set format for all cells)
 * This class stores the rows for the {@link ezcConsoleTable} class.
 * 
 * @package ConsoleTools
 * @version //autogen//
 */
class ezcConsoleTableRow implements Countable, Iterator, ArrayAccess {

    /**
     * Set the format applied to the borders of this row. 
     * 
     * @see ezcConsoleOutput
     * 
     * @var string
     */
    public $format = 'default';

    /**
     * The cells of the row. 
     * 
     * @var array(ezcConsoleTableCell)
     */
    protected $cells = array();

    /**
     * Create a new ezcConsoleProgressbarRow. 
     * Creates a new ezcConsoleProgressbarRow. 
     * 
     */
    public function __construct()
    {
        if ( func_num_args() > 0 )
        {
            foreach ( func_get_args() as $arg )
            {
                if ( !( $arg instanceof ezcConsoleTableCell ) )
                {
                    throw new ezcBaseTypeException( 'ezcConsoleTableCell', gettype( $arg ) );
                }
                $this->cells[] = $arg;
            }
        }
    }

    public function offsetExists( $offset )
    {
        if ( !is_int( $offset ) || $offset < 0 )
        {
            throw new ezcBaseTypeException( 'int+', gettype( $offset ) );
        }
        return isset( $this->cells[$offset] );
    }

    public function offsetGet( $offset )
    {
        if ( !isset( $offset ) )
        {
            $cellKeys = array_keys( $this->cells );
            $offset = end( $cellKeys ) + 1;
            $this->cells[] = new ezcConsoleTableCell();
        }
        if ( !is_int( $offset ) || $offset < 0 )
        {
            throw new ezcBaseTypeException( 'int+', gettype( $offset ) );
        }
        if ( !isset( $this->cells[$offset] ) )
        {
            $this->cells[$offset] = new ezcConsoleTableCell();
        }
        return $this->cells[$offset];
    }

    public function offsetSet( $offset, $value )
    {
        if ( !( $value instanceof ezcConsoleTableCell ) )
        {
            throw new ezcBaseTypeException( 'ezcConsoleTableCell', gettype( $value ) );
        }
        if ( !is_int( $offset ) || $offset < 0 )
        {
            throw new ezcBaseTypeException( 'int+', gettype( $offset ) );
        }
        $end = end( array_keys( $offset ) );
        if ( $offset > $end ) 
        {
            // Autocreate missing cells to the left
            $i = $offset;
            while ( $i > $end )
            {
                if ( !isset( $this->cells[$i] ) )
                {
                    $this->cells[$i--] = new ezcConsoleTableCell();
                }
            }
        }
        $this->cells[$offset] = $value;
    }

    public function offsetUnset( $offset )
    {
        if ( !is_int( $offset ) || $offset < 0 )
        {
            throw new ezcBaseTypeException( 'int+', gettype( $offset ) );
        }
        if ( isset( $this->cells[$offset] ) )
        {
            unset( $this->cells[$offset] );
        }
    }

    public function append( ezcConsoleTableCell $value )
    {
        $this->cells[] = $value;
    }

    public function count()
    {
        return count( $this->cells );
    }

    public function current()
    {
        return current( $this->cells );
    }

    public function key()
    {
        return key( $this->cells );
    }

    public function next()
    {
        return next( $this->cells );
    }

    public function rewind()
    {
        return reset( $this->cells );
    }

    public function valid()
    {
        return current( $this->cells ) !== false;
    }

}

?>
