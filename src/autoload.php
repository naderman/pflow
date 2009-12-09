<?php

require 'SplClassLoader.php';

// pFlow's autoloader
$pFlowClassLoader = new SplClassLoader('\pFlow');
$pFlowClassLoader->register();

// eZ Components' autoloader
// try to find an SVN, Release or PEAR version of base.php
foreach (array('Base/src/base.php', 'Base/base.php', 'ezc/Base/base.php') as $ezcBaseFileToInclude) {
    if (!in_array('ezcBase', get_declared_classes())) {
        @include_once $ezcBaseFileToInclude;
    } else {
        break;
    }
}
// remove the global variable used in the foreach loop
unset($ezcBaseFileToInclude);

spl_autoload_register(array('ezcBase', 'autoload'));


// static-reflection's autoloader
//require_once 'static-reflection/source/Autoloader.php';
//spl_autoload_register(array(new org\pdepend\reflection\Autoloader, 'autoload'));

// static-reflection via SPLClassLoader
$staticReflectionClassLoader = new SplClassLoader('\org\pdepend\reflection', 'static-reflection/source/');
$staticReflectionClassLoader->register();
