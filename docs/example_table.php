<?php
/**
 * Example for the usage of eczConsoleTable class.
 *
 * @package ConsoleTools
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 */

// Prepare console output options
$opts = array(
    'verboseLevel'  => 1,           // print verbosity levels 0 and 1 only
    'autobreak'     => 80,          // will break lines every 80 chars
);

// Initialize the console outputer
$out = new ezcConsoleOutput( $opts );

$tableOpts = array(
    'lineColorHead' => 'red',  // Make header rows surrounded by red lines
);

// Initialize table with options, width of 60 chars and 3 cols
$table = new ezcConsoleTable( $out, array( 'width' => 60, 'cols' => 3 ), $tableOpts );

// Generate a header row ( red color )
$table->addRowHead( array( 'First col', 'Second col', 'Third col' ) );

// Add some data ( right column will be largest )
$table->addRow( array( 'Data', 'Data', 'Very very very very very long data' ) );

// Add some more data ( middle column data will be green )
$table->addRow( array( 'More', $out->styleText( 'More green', 'green' ), 'Smaller data' ) );

// Print table to the screen
$table->output();

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
