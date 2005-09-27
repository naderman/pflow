<?php
/**
 * Example for the usage of eczConsoleParameter class.
 *
 * @package ConsoleTools
 * @version //autogen//
 * @copyright Copyright ( C ) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 */

// Prepare parameter handler
$paramHandler = new ezcConsoleParameter();

// Options for the help flag
$help = array(
 'short' => 'Get help output.',
 'long'  => 'Retreive help on the usage of this command.',
);

// Register parameter -h/--help with texts from above
$paramHandler->registerParam( 'h', 'help', $help );

// Options for the file parameter
$file = array(
 // Must have a value, type string
 'type'     => ezcConsoleParameter::TYPE_STRING
 'short'    => 'Process a file.',
 'long'     => 'Processes a single file.',
 // May not be used in combination with -d/--directory
 'excludes' => array( 'd' ),
)

// Register parameter -f/--file with options from above
$paramHandler->registerParam( 'f', 'file', $file );

// Options for dir parameter
$dir = array(
 'type'     => ezcConsoleParameter::TYPE_STRING
 'short'    => 'Process a directory.',
 'long'     => 'Processes a complete directory.',
 // May not be used with -f/--file together
 'excludes' => array( 'f' ),
)

// Register -d/--dir parameter
$paramHandler->registerParam( 'd', 'dir', $dir );

// Register the alias --directory for -d/--dir
$paramHandler->registerAlias( 'd', 'directory', 'd' );


// ... initialize ezcConsoleOutput or similar to output stuff...

// Process parameters given

try 
{
     // Processing
     $paramHandler->processParams();
} 
catch ( ezcConsoleParameterException $e ) 
{
    // An error occured
    if ( $e->code === ezcConsoleParameterException::CODE_DEPENDENCY ) 
    {
        // Output some error text
        $consoleOut->outputText(
            'Parameter '.$e->paramName." may not occur here.\n\n", 'error'
        );
        // And output some help on the parameter.
        $consoleOut->output(
            $paramHandler->getHelp( $e->paramName )."\n"
        );
    }
    // End the program
    exit( $e->code );
}

// Ok, everything went well

if ( $res = $paramHandler->getParam( 'h' ) )
{
    // Help was requested. Output Help text as "info".
    $consoleOut->output(
        $paramHandler->getHelp(), 'info'
    );
    exit;
}

if ( $res = $paramHandler->getParam( 'f' ) ) 
{
    // -f/--file was set. Value now in res.
    $file = $res;
}

if ( $res = $paramHandler->getParam( 'f' ) )
{
    // -d/--dir/--directory was set. Value now in res.
    $file = $res;
}

processSomethingOn( $file );

exit( 0 );

?>
