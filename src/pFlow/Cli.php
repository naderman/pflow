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

/**
 * console front-end to use pFlow from the command line
 *
 * This class requires eZ Components autoloader to be configured
 * because it uses ezcConsoleTools
 *
 * @package    pFlow
 * @author     Falko Menge <fakko at users dot sourceforge dot net>
 * @author     Nils Adermann <naderman at naderman dot de>
 * @copyright  2009 Falko Menge, Nils Adermann
 * @license    http://www.gnu.org/licenses/lgpl.txt
 *             GNU Lesser General Public License
 */
class Cli
{
    const VERSION = '0.1';

    /**
     * @var \pFlow\AnalyzerInterface
     */
    protected $analyzer;

    /**
     * @var \ezcConsoleInput
     */
    protected $input;

    /**
     * Constructor which requires an analyzer which is configured with the cli
     * options.
     * @param  \pFlow\AnalyzerInterface $analyzer
     * @param  \ezcConsoleInput         $input
     * @return Cli
     */
    public function __construct(AnalyzerInterface $analyzer, \ezcConsoleInput $input)
    {
        $this->analyzer = $analyzer;
        $this->input = $input;
    }

    /**
     * Sets up the CLI option & argument definitions on the ezcConsoleInput member.
     * @return void
     */
    protected function setupInput()
    {
        $this->input->registerOption(
            new \ezcConsoleOption(
                'r',
                'recursive',
                \ezcConsoleInput::TYPE_NONE
            )
        );
        $this->input->getOption('recursive')->shorthelp = 'Analyze directories recursively';

        $this->input->registerOption(
            new \ezcConsoleOption(
                'h',
                'help'
            )
        );
        $this->input->getOption('help')->isHelpOption = true; // if parameter is set, all options marked as mandatory may be skipped
        $this->input->getOption('help')->shorthelp = 'Display help';

        $this->input->argumentDefinition = new \ezcConsoleArguments();
        $this->input->argumentDefinition[0] = new \ezcConsoleArgument(
            'sources',
            \ezcConsoleInput::TYPE_STRING,
            'Files and/or directories to analyze',
            '',
            true,
            true
        );
    }

    /**
     * starts the command line interface for pFlow
     * @return void
     */
    public function run()
    {
        $this->setupInput();

        try
        {
            $this->input->process();
        }
        catch (\ezcConsoleOptionException $e)
        {
            die($e->getMessage() . "\nTry option -h to get a list of available options.\n");
        }
        catch (\ezcConsoleArgumentMandatoryViolationException $e)
        {
            die($e->getMessage() . "\nTry option -h to get a list of available options.\n");
        }

        if ($this->input->helpOptionSet())
        {
            echo $this->input->getHelpText(
                 "\npFlow version " . self::VERSION . "\n\n"
                 . "A tool for analysing control and data flow in PHP applications."
            );
        }
        else
        {
            // start generation
            $this->analyzer->setSources($this->input->getArguments(), $this->input->getOption('recursive')->value);
        }
    }
}
