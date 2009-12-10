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

require_once __DIR__ . '/../src/autoload.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Base test case for pFlow tests.
 *
 * @package    pFlow
 * @author     Falko Menge <fakko at users dot sourceforge dot net>
 * @author     Nils Adermann <naderman at naderman dot de>
 * @copyright  2009 Falko Menge, Nils Adermann
 * @license    http://www.gnu.org/licenses/lgpl.txt
 *             GNU Lesser General Public License
 */
abstract class BaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Asserts that the two passed arrays contain exactly the same elements.
     * It ignores keys, so it treats each array as a set.
     *
     * @param  array  $expected Expected set
     * @param  array  $actual   Actual set
     * @param  string $message  Optional message, displayed on failure
     * @return void
     */
    public function assertSetEquals(array $expected, array $actual, $message = '')
    {
        if (!empty($message))
        {
            $message .= ': ';
        }

        foreach ($expected as $expectedElement)
        {
            $this->assertContains($expectedElement, $actual, $message . ' element missing');
        }

        $this->assertEquals(
            count($expected),
            count($actual),
            $message . ' too many elements in result'
        );
    }

    /**
     * Asserts that the expected result is a subset of the actual result set.
     * It ignores keys, so it treats each array as a set.
     *
     * @param  array  $expected Expected set (should be subset)
     * @param  array  $actual   Actual set (should be superset)
     * @param  string $message  Optional message, displayed on failure
     * @return void
     */
    public function assertSubset(array $expected, array $actual, $message = '')
    {
        if (!empty($message))
        {
            $message .= ': ';
        }

        foreach ($expected as $expectedElement)
        {
            $this->assertContains($expectedElement, $actual, $message . ' element missing');
        }
    }
}