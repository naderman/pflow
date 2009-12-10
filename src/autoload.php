<?php
/**
 * This file is part of pFlow.
 *
 * pFlow is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation; either version 3 of
 * the License, or (at your option) any later version.
 *
 * pFlow is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    pFlow
 * @author     Falko Menge <fakko at users dot sourceforge dot net>
 * @author     Nils Adermann <naderman at naderman dot de>
 * @copyright  2009 Falko Menge, Nils Adermann
 * @license    http://www.gnu.org/licenses/lgpl.txt
 *             GNU Lesser General Public License
 */

// set up include path
set_include_path(
    // pFlow
    realpath(dirname(__FILE__) . '/../src') . PATH_SEPARATOR
    // eZ Components
    . realpath(dirname(__FILE__) . '/../lib/ezc/trunk/') . PATH_SEPARATOR
    // other included libraries
    . realpath(dirname(__FILE__) . '/../lib/') . PATH_SEPARATOR
    // original include path from php.ini
    . get_include_path() . PATH_SEPARATOR
    // PEAR files shipped with pFlow (only used if no PEAR installed)
    . realpath(dirname(__FILE__) . '/../lib/pear')
);

require 'SplClassLoader.php';

// pFlow's autoloader
$pFlowClassLoader = new SplClassLoader('pFlow');
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
require_once 'static-reflection/source/Autoloader.php';
spl_autoload_register(array(new org\pdepend\reflection\Autoloader, 'autoload'));

// static-reflection via SplClassLoader
//$staticReflectionClassLoader = new SplClassLoader('org\pdepend\reflection', 'static-reflection/source/');
//$staticReflectionClassLoader->register();
