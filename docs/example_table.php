<?php
/**
 * Example for the usage of eczConsoleTable class.
 *
 * @package ConsoleTools
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Autoload ezc classes 
 * 
 * @param string $class_name 
 */
function __autoload( $class_name )
{
    require_once("Base/trunk/src/base.php");
    if ( strpos( $class_name, "_" ) !== false )
    {
        $file = str_replace( "_", "/", $class_name ) . ".php";
        $val = require_once( $file );
        if ( $val == 0 )
            return true;
        return false;
    }
    ezcBase::autoload( $class_name );
}

// Prepare console output options
$opts = array(
    'verboseLevel'  => 1,           // print verbosity levels 0 and 1 only
    'autobreak'     => 80,          // will break lines every 80 chars
);

// Initialize the console outputer
$out = new ezcConsoleOutput( $opts );

$tableOpts = array(
    'lineFormatHead' => 'red',  // Make header rows surrounded by red lines
);

// Initialize table with options, width of 60 chars and 3 cols
$table = new ezcConsoleTable( $out, array( 'width' => 60, 'cols' => 3 ), $tableOpts );

// Generate a header row ( red color )
$table->addHeadRow( array( 'First col', 'Second col', 'Third col' ) );

// Add some data ( right column will be largest )
$table->addRow( array( 'Data', 'Data', 'Very very very very very long data' ) );

// Add some more data 
$table->addRow( array( 'More', 'More green', 'Smaller data' ) );

// Print table to the screen
$table->outputTable();

/*
RESULT ( without color ):

+---------------------------------------------------------+
| First col | Second col | Third col                      |     // Red surrounding
+---------------------------------------------------------+
| Data      | Data       | Very very very very very long  |     // Auto breaking in cells
|           |            | data                           |     // and auto col sizing
+---------------------------------------------------------+
| More      | More green | Smaller data                   |     // Green color in middle text
+---------------------------------------------------------+
*/
?>
