<?php
/**
 * Example for the usage of eczConsoleParameter class.
 *
 * @package ConsoleTools
 * @version //autogen//
 * @copyright Copyright ( C ) 2005, 2006 eZ systems as. All rights reserved.
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

$paramHandler = new ezcConsoleInput();

// Register simple parameter -h/--help
$paramHandler->registerOption( new ezcConsoleOption( 'h', 'help' ) );

// Register complex parameter -f/--file
$file = new ezcConsoleOption(
 'f',
 'file',
 ezcConsoleInput::TYPE_STRING,
 null,
 false,
 'Process a file.',
 'Processes a single file.'
);
$paramHandler->registerOption( $file );

// Manipulate parameter -f/--file after registration
$file->multiple = true;

// Register another complex parameter that depends on -f and excludes -h
$dir = new ezcConsoleOption(
 'd',
 'dir',
 ezcConsoleInput::TYPE_STRING,
 null,
 true,
 'Process a directory.',
 'Processes a complete directory.',
 array( new ezcConsoleOptionRule( $paramHandler->getOption( 'f' ) ) ),
 array( new ezcConsoleOptionRule( $paramHandler->getOption( 'h' ) ) )
);
$paramHandler->registerOption( $dir );

// Register an alias for this parameter
$paramHandler->registerAlias( 'e', 'extended-dir', $dir );

// Process registered parameters and handle errors
try
{
     $paramHandler->process( array( 'example_input.php', '-h' ) );
}
catch ( ezcConsoleInputException $e )
{
     if ( $e->getCode() === ezcConsoleInputException::PARAMETER_DEPENDENCY_RULE_NOT_MET )
     {
         $consoleOut->outputText(
             'Parameter ' . isset( $e->param ) ? $e->param->name : 'unknown' . " may not occur here.\n", 'error'
         );
     }
     exit( 1 );
}

// Process a single parameter
$file = $paramHandler->getOption( 'f' );
if ( $file->value === false )
{
     echo "Parameter -{$file->short}/--{$file->long} was not submitted.\n";
}
elseif ( $file->value === true )
{
     echo "Parameter -{$file->short}/--{$file->long} was submitted without value.\n";
}
else
{
     echo "Parameter -{$file->short}/--{$file->long} was submitted with value <".var_export($file->value, true).">.\n";
}

// Process all parameters at once:
foreach ( $paramHandler->getOptionValues() as $paramShort => $val )
{
     switch (true)
     {
         case $val === false:
             echo "Parameter $paramShort was not submitted.\n";
             break;
         case $val === true:
             echo "Parameter $paramShort was submitted without a value.\n";
             break;
         case is_array($val):
             echo "Parameter $paramShort was submitted multiple times with value: <".implode(', ', $val).">.\n";
             break;
         default:
             echo "Parameter $paramShort was submitted with value: <$val>.\n";
             break;
     }
}
?>
