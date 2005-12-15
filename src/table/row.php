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
        return isset( $this->cells[$offset] );
    }

    /**
     * Returns the element with the given offset. 
     * This method is part of the ArrayAccess interface to allow access to the
     * data of this object as if it was an array. In case of the
     * ezcConsoleTableRow class this method always returns a valid cell object
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
            $this->cells[$offset] = new ezcConsoleTableCell();
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
        $this->cells[$offset] = $value;
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
        if ( isset( $this->cells[$offset] ) )
        {
            unset( $this->cells[$offset] );
        }
    }

    /**
     * Returns the number of cells in the row.
     * This method is part of the Countable interface to allow the usage of
     * PHP's count() function to check how many cells this row has.
     *
     * @returns int Number of cells in this row.
     */
    public function count()
    {
        $keys = array_keys( $this->cells );
        return count( $keys ) > 0 ? ( end( $keys ) + 1 ) : 0;
    }

    /**
     * Returns the currently selected cell.
     * This method is part of the Iterator interface to allow acces to the 
     * cells of this row by iterating over it like an array (e.g. using
     * foreach).
     * 
     * @returns object(ezcConsoleTableCell) The currently selected cell.
     */
    public function current()
    {
        return current( $this->cells );
    }

    /**
     * Returns the key of the currently selected cell.
     * This method is part of the Iterator interface to allow acces to the 
     * cells of this row by iterating over it like an array (e.g. using
     * foreach).
     * 
     * @returns int The key of the currently selected cell.
     */
    public function key()
    {
        return key( $this->cells );
    }

    /**
     * Returns the next cell and selects it or false on the last cell.
     * This method is part of the Iterator interface to allow acces to the 
     * cells of this row by iterating over it like an array (e.g. using
     * foreach).
     *
     * @returns mixed ezcConsoleTableCell if the next cell exists, or false.
     */
    public function next()
    {
        return next( $this->cells );
    }

    /**
     * Selects the very first cell and returns it.
     * This method is part of the Iterator interface to allow acces to the 
     * cells of this row by iterating over it like an array (e.g. using
     * foreach).
     *
     * @returns ezcConsoleTableCell The very first cell of this row.
     */
    public function rewind()
    {
        return reset( $this->cells );
    }

    /**
     * Returns if the current cell is valid.
     * This method is part of the Iterator interface to allow acces to the 
     * cells of this row by iterating over it like an array (e.g. using
     * foreach).
     *
     * @returns ezcConsoleTableCell The very first cell of this row.
     */
    public function valid()
    {
        return current( $this->cells ) !== false;
    }

}

?>
