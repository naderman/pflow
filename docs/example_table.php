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

// Initialize the console output handler
$out = new ezcConsoleOutput();
// Define a new format "headline"
$out->formats->headline->color = 'red';
$out->formats->headline->style = array( 'bold' );
// Define a new format "sum"
$out->formats->sum->color = 'blue';
$out->formats->sum->style = array( 'negative' );

// Create a new table
$table = new ezcConsoleTable( $out, 60, 1 );

// Create first row and in it the first cell
$table[0][0]->content = 'Headline 1';

// Create 3 more cells in row 0
for ( $i = 2; $i < 5; $i++ )
{
     $table[0][]->content = "Headline $i";
}

$data = array( 1, 2, 3, 4);

// Create some more data in the table...
foreach ( $data as $value )
{
     // Create a new row each time and set it's contents to the actual value
     $table[][0]->content = $value;
}

// Set another border format for our headline row
$table[0]->borderFormat = 'headline';

// Set the content format for all cells of the 3rd row to "sum"
$table[2]->format = 'sum';

$table->outputTable();

/*

RESULT (without color):

+------------+------------+------------+------------+       //
| Headline 1 | Headline 2 | Headline 3 | Headline 4 |       // Red bordered line
+------------+------------+------------+------------+       //
| 1          |            |            |            |
+------------+------------+------------+------------+
| 2          |            |            |            |       // Content printed in white on blue
+------------+------------+------------+------------+
| 3          |            |            |            |
+------------+------------+------------+------------+
| 4          |            |            |            |
+------------+------------+------------+------------+

*/
?>
