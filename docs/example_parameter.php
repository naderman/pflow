<?php
/**
 * Example for the usage of eczConsoleParameter class.
 *
 * @package ConsoleTools
 * @version //autogen//
 * @copyright Copyright ( C ) 2005 eZ systems as. All rights reserved.
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

// Prepare parameter handler
$paramHandler = new ezcConsoleParameter();

// Options for the help flag
$help = array(
    'shorthelp' => 'Get help output.',
    'longhelp'  => 'Retreive help on the usage of this command.',
);

// Register parameter -h/--help with texts from above
$paramHandler->registerParam( 'h', 'help', $help );

// Options for the file parameter
$file = array(
    // Must have a value, type string
    'type'     => ezcConsoleParameter::TYPE_STRING,
    'shorthelp'    => 'Process a file.',
    'longhelp'     => 'Processes a single file.',
    // May not be used in combination with -d/--directory
    'excludes' => array( 'd' ),
);

// Register parameter -f/--file with options from above
$paramHandler->registerParam( 'f', 'file', $file );

// Options for dir parameter
$dir = array(
    'type'     => ezcConsoleParameter::TYPE_STRING,
    'shorthelp'    => 'Process a directory.',
    'longhelp'     => 'Processes a complete directory.',
    // May not be used with -f/--file together
    'excludes' => array( 'f' ),
);

// Register -d/--dir parameter
$paramHandler->registerParam( 'd', 'dir', $dir );

// Register the alias --directory for -d/--dir
$paramHandler->registerAlias( 'd', 'directory', 'd' );


// ... initialize ezcConsoleOutput or similar to output stuff...

// Process parameters given

try 
{
     // Processing
     $paramHandler->process();
} 
catch ( ezcConsoleParameterException $e ) 
{
    // An error occured
    if ( $e->code === ezcConsoleParameterException::PARAMETER_DEPENDENCY_RULE_NOT_MET ) 
    {
        // Output some error text
        $consoleOut->outputText(
            "Parameter <{$e->paramName}> may not occur here.\n\n", 'error'
        );
    }
    // End the program
    exit( $e->code );
}

// Ok, everything went well

if ( $res = $paramHandler->getParam( '-h' ) )
{
    // Help was requested. Output Help text as "info".
    foreach ( $paramHandler->getHelp() as $paramHelp ) 
    {
        echo $paramHelp[0] . "\t" . $paramHelp[1] . "\n";
    }
    echo "\n";
    exit;
}

if ( $res = $paramHandler->getParam( '-f' ) ) 
{
    // -f/--file was set. Value now in res.
    $file = $res;
} else if ( $res = $paramHandler->getParam( '-d' ) )
{
    // -d/--dir/--directory was set. Value now in res.
    $file = $res;
}

echo "Successfully processed ". var_export($file, true) . "\n";

exit( 0 );
?>
