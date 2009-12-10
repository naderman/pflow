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
    /**
     * @var \pFlow\AnalyzerInterface
     */
    protected $analyzer;

    /**
     * Constructor which requires an analyzer which is configured with the cli
     * options.
     * @param  \pFlow\AnalyzerInterface $analyzer
     * @return Cli
     */
    public function __construct(AnalyzerInterface $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    /**
     * starts the command line interface for pFlow
     * @return void
     */
    public function run()
    {
        $input = new ezcConsoleInput();

        $recursiveOption = $input->registerOption(
            new ezcConsoleOption(
                'r',
                'recursive',
                ezcConsoleInput::TYPE_NONE
            )
        );
        $recursiveOption->shorthelp = 'Analyze directories recursively';

        $helpOption = $input->registerOption(
            new ezcConsoleOption(
                'h',
                'help'
            )
        );
        $helpOption->isHelpOption = true; // if parameter is set, all options marked as mandatory may be skipped
        $helpOption->shorthelp = 'Display help';

        $input->argumentDefinition = new ezcConsoleArguments();
        $input->argumentDefinition[0] = new ezcConsoleArgument(
            'sources',
            ezcConsoleInput::TYPE_STRING,
            'Files and/or directories to analyze',
            '',
            true,
            true
        );

        try
        {
            $input->process();
        }
        catch (ezcConsoleOptionException $e)
        {
            die($e->getMessage() . "\nTry option -h to get a list of available options.\n");
        }
        catch (ezcConsoleArgumentMandatoryViolationException $e)
        {
            die($e->getMessage() . "\nTry option -h to get a list of available options.\n");
        }

        if ($helpOption->value === true)
        {
            echo $input->getHelpText(
                 "\npFlow v" . pFlow\pFlow::VERSION . "\n\n"
                 . "A tool for analysing control and data flow in PHP applications."
            );
        }
        else
        {
            // start generation
            $this->analyzer->setSources($input->getArguments(), $recursiveOption->value);
        }
    }
}
