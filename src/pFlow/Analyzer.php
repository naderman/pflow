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

use pdepend\reflection\ReflectionSession;

/**
 * Source code analyzer.
 *
 * Uses static reflection and ezcReflection to analyze source code.
 *
 * @package    pFlow
 * @author     Falko Menge <fakko at users dot sourceforge dot net>
 * @author     Nils Adermann <naderman at naderman dot de>
 * @copyright  2009 Falko Menge, Nils Adermann
 * @license    http://www.gnu.org/licenses/lgpl.txt
 *             GNU Lesser General Public License
 */
class Analyzer implements AnalyzerInterface
{
    /**
     * @var string[]
     */
    protected $files = array();

    /**
     * Sets which sources shall be analyzed.
     *
     * @param  string[] $sources   paths for directories or files
     * @param  bool     $recursive Whether directories shall be analyzed
     *                             recursively
     * @return void
     */
    public function setSources(array $sources, $recursive)
    {
        $files = array();

        foreach ($sources as $source)
        {
            if (is_file($source))
            {
                $this->addFile($source);
            }
            else
            {
                if ($recursive)
                {
                    $dir = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source));
                }
                else
                {
                    $dir = new \DirectoryIterator($source);
                }

                foreach ($dir as $file)
                {
                    if ($file->isFile())
                    {
                        $this->addFile($file->getRealPath());
                    }
                }
            }
        }
    }

    /**
     * Adds a file to the list of files to be analyzed.
     *
     * Makes sure the file is not excluded.
     *
     * @param  string $path Absolute or relative path or filename.
     * @return void
     */
    public function addFile($path)
    {
        $this->files[] = realpath($path);
    }

    /**
     * Retrieve the list of files that will be or have been analyzed.
     *
     * @return array Absolute paths to PHP files for analysis
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Analyzes the given files
     *
     * @return void
     */
    public function analyze()
    {
        $staticReflection = ReflectionSession::createStaticSession(new \pdepend\reflection\resolvers\NullNamingResolver());

        $fileSetQuery = $staticReflection->createFileSetQuery();
        $topLevelItems = $fileSetQuery->find($this->files);

        foreach ($topLevelItems as $item)
        {
            // analyze item
        }
    }
}
