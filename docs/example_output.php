<?php
/**
 * Example for the usage of eczConsoleOutput class.
 *
 * @package ConsoleTools
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 */

// Prepare console output options
$opts = array(
 'verboseLevel'  => 1,           // print verbosity levels 0 and 1 only
 'autobreak'     => 40,          // will break lines every 40 chars
 'styles'        => array(
     'default'   => 'green',     // green default text
     'success'   => 'white',     // white success messages
 ),
);

// Initialize the console outputer
$out = new ezcConsoleOutput( $opts );

// Print some normal text ( will be green, see options )
$out->outputText( "Welcome to my cool program!\n" );

// Output a success messagen
$out->outputText( "You successfully managed to start the program!\n", 'success' );

// Output an error message in default text
$out->outputText( "Sorry, there was an error: " );
$out->outputText( "Your computer does not support PHP 6. ", 'error' );
$out->outputText( "Please consider upgrading!" );

// Output text only for verbosity 10 ( default style )
$out->outputText( "Some verbose output.\n", null, 10 );   // With current options, not printed

// Output some bold text
$out->outputText( "And some not so verbose, bold output.\n", 'bold' );
?>
