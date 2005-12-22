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

$out->formats->evenRow->color = 'red';
$out->formats->evenRow->style = array( 'bold' );

$out->formats->oddRow->color = 'blue';
$out->formats->oddRow->style = array( 'bold' );

$out->formats->evenCell->color = 'red';
$out->formats->evenCell->style = array( 'negative' );

$out->formats->oddCell->color = 'blue';
$out->formats->oddCell->style = array( 'negative' );

// Create a new table
$table = new ezcConsoleTable( $out, 60 );

for ( $i = 0; $i < 5; $i ++ )
{
    for ( $j = 0; $j < 5; $j++ )
    {
        $table[$i][$j]->content = '##';
        if ( $i === $j )
        {
            $table[$i][$j]->format = $j % 2 == 0 ? 'evenCell' : 'oddCell';
        }
    }
    $table[$i]->format = $i % 2 == 0 ? 'evenRow' : 'oddRow';
    $table[$i]->borderFormat = $i % 2 == 0 ? 'evenRow' : 'oddRow';
    $table[$i]->align = ezcConsoleTable::ALIGN_CENTER;
}

$table->outputTable();
echo "\n";
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
