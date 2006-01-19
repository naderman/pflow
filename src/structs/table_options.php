<?php
/**
 * File containing the ezcConsoleTableOptions class.
 *
 * @package ConsoleTools
 * @version //autogentag//
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Struct class to store the options of the ezcConsoleTable class.
 *
 * This class stores the options for the {@link ezcConsoleTable} class.
 * 
 * @package ConsoleTools
 * @version //autogen//
 */
class ezcConsoleTableOptions
{
    /**
     * Column width, either a fixed int value (number of chars)  or 'auto'.
     * 
     * @var mixed
     */
    public $colWidth = 'auto';

    /**
     * Wrap style of text contained in strings.
     * @see ezcConsoleTable::WRAP_AUTO
     * @see ezcConsoleTable::WRAP_NONE
     * @see ezcConsoleTable::WRAP_CUT
     * 
     * @var int
     */
    public $colWrap = ezcConsoleTable::WRAP_AUTO;

    /**
     * Standard column alignment, applied to cells that have to explicit
     * alignment assigned.
     *
     * @see ezcConsoleTable::ALIGN_LEFT
     * @see ezcConsoleTable::ALIGN_RIGHT
     * @see ezcConsoleTable::ALIGN_CENTER
     * @see ezcConsoleTable::ALIGN_DEFAULT
     * 
     * @var int
     */
    public $defaultAlign = ezcConsoleTable::ALIGN_LEFT;

    /**
     * Padding characters for side padding between data and lines. 
     * 
     * @var string
     */
    public $colPadding = ' ';

    /**
     * Type of the given table width (fixed or maximal value).
     * 
     * @var int
     */
    public $widthType = ezcConsoleTable::WIDTH_MAX;
        
    /**
     * Character to use for drawing vertical lines. 
     * 
     * @var string
     */
    public $lineVertical = '-';

    /**
     * Character to use for drawing hozizontal lines. 
     * 
     * @var string
     */
    public $lineHorizontal = '|';

    /**
     * Character to use for drawing line corners.
     * 
     * @var string
     */
    public $corner = '+';
    
    /**
     * Standard column content format, applied to cells that have 'default' as
     * the content format.
     * 
     * @var string
     */
    public $defaultFormat = 'default';

    /**
     * Standard border format, applied to rows that have 'default' as the
     * border format.
     * 
     * @var string
     */
    public $defaultBorderFormat = 'default';

    /**
     * Create a new ezcConsoleProgressbarOptions struct. 
     *
     * Create a new ezcConsoleProgressbarOptions struct for use with {@link
     * ezcConsoleOutput}. 
     * 
     * @todo documentation missing!
     */
    public function __construct( 
        $colWidth = 'auto',
        $colWrap = ezcConsoleTable::WRAP_AUTO,
        $defaultAlign = ezcConsoleTable::ALIGN_LEFT,
        $colPadding = ' ',
        $widthType = ezcConsoleTable::WIDTH_MAX,
        $lineVertical = '-',
        $lineHorizontal = '|',
        $corner = '+',
        $defaultFormat = 'default',
        $defaultBorderFormat = 'default'
    )
    {
        $this->colWidth = $colWidth;
        $this->colWrap = $colWrap;
        $this->defaultAlign = $defaultAlign;
        $this->colPadding = $colPadding;
        $this->widthType = $widthType;
        $this->lineVertical = $lineVertical;
        $this->lineHorizontal = $lineHorizontal;
        $this->corner = $corner;
        $this->defaultFormat = $defaultFormat;
        $this->defaultBorderFormat = $defaultBorderFormat;
    }

}

?>
