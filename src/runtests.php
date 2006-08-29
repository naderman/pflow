<?php
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter( __FILE__ );

// All errors must be reported
error_reporting( E_ALL | E_STRICT );
require_once("Base/src/base.php");

function __autoload( $className )
{
    if ( strpos( $className, "_" ) !== false )
    {
        $file = str_replace( "_", "/", $className ) . ".php";
        $val = require_once( $file );
        if ( $val == 0 )
            return true;
        return false;
    }
    ezcBase::autoload( $className );
}

ezcTestRunner::main();
?>
