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

namespace pFlow;

require_once __DIR__ . '/BaseTest.php';

/**
 * Test case for the reflection autoloader.
 *
 * @package    pFlow
 * @author     Falko Menge <fakko at users dot sourceforge dot net>
 * @author     Nils Adermann <naderman at naderman dot de>
 * @copyright  2009 Falko Menge, Nils Adermann
 * @license    http://www.gnu.org/licenses/lgpl.txt
 *             GNU Lesser General Public License
 */
class AnalyzerTest extends BaseTest
{
    /**
     * @return void
     * @covers \pFlow\Analyzer<extended>
     * @group pflow
     * @group unittest
     */
    public function testSetSourceDirectory()
    {
        $analyzer = new Analyzer;
        $analyzer->setSources(array(__DIR__ . '/data'), true);

        $resultFiles = $analyzer->getFiles();
        $expectedFiles = array(
            __DIR__ . "/data/MainClass.php",
            __DIR__ . "/data/module1/Module1.php",
        );

        $this->assertSubset(
            $expectedFiles,
            $resultFiles,
            "recursive file search of data directory failed"
        );
    }
}