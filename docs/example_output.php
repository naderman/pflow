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

// Prepare console output options
$opts = array(
 'verboseLevel'  => 1,           // print verbosity levels 0 and 1 only
 'autobreak'     => 40,          // will break lines every 40 chars
 'format'        => array(
     'default'   => array(
        'color' => 'green'
     ),     // green default text
     'success'   => array(
        'color' => 'blue'
     ),     // blue success messages
 ),
);

// Initialize the console outputer
$out = new ezcConsoleOutput( $opts );

// Print some normal text ( will be green, see options )
$out->outputText( "Welcome to my cool program!\n" );

// Output a success message
$out->outputText( "You successfully managed to start the program!\n", 'success' );

// Output an error message in default text
$out->outputText( "Sorry, there was an error: " );
$out->outputText( "Your computer does not support PHP 6. ", 'failure' );
$out->outputText( "Please consider upgrading! An this is a very very very very long text text text text." );

// Output text only for verbosity 10 ( default style )
$out->outputText( "Some verbose output.\n", null, 10 );   // With current options, not printed

// Output some bold text
$out->outputText( "And some not so verbose, bold output.\n", 'bold' );
?>
