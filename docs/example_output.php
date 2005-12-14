<?php
/**
 * Example for the usage of eczConsoleOutput class.
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


// Create the output handler
$out = new ezcConsoleOutput();

// Set the verbosity to level 10
$out->options->verboseLevel = 10;
// Enable auto wrapping of lines after 40 characters
$out->options->autobreak    = 40;

// Set the color of the default output format to green
$out->formats->default->color   = 'green';

// Set the color of the output format named 'success' to white
$out->formats->success->color   = 'white';
// Set the style of the output format named 'success' to bold
$out->formats->success->style   = array( 'bold' );

// Set the color of the output format named 'failure' to red
$out->formats->failure->color   = 'red';
// Set the style of the output format named 'failure' to bold
$out->formats->failure->style   = array( 'bold' );
// Set the background color of the output format named 'failure' to blue
$out->formats->failure->bgcolor = 'blue';

// Output text with default format
$out->outputText( 'This is default text ' );
// Output text with format 'success'
$out->outputText( 'including success message', 'success' );
// Some more output with default output.
$out->outputText( "and a manual linebreak.\n" );

// Manipulate the later output
$out->formats->success->color = 'green';
$out->formats->default->color = 'blue';

// This is visible, since we set verboseLevel to 10, and printed in default format (now blue)
$out->outputText( "Some verbose output.\n", null, 10 );
// This is visible, since we set verboseLevel to 10, and printed in format 'failure'
$out->outputText( "And some not so verbose, failure output.\n", 'failure', 5 );
?>
