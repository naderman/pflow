<?php

function __autoload( $class_name )
{
    require_once("Base/trunk/src/base.php");
    ezcBase::autoload( $class_name );
}

// Remove this file name from the assertion trace.
require_once 'PHPUnit2/Util/Filter.php';
PHPUnit2_Util_Filter::addFileToFilter(__FILE__);

ezcTestRunner::main();

?>
