<?php

require_once 'PHPUnit2/TextUI/ResultPrinter.php';

class ezcTestPrinter extends PHPUnit2_TextUI_ResultPrinter
{
    /**
     * Overrides ResultPrinter::nextColumn method to get rid of to automatic 
     * newline inserts.
     */
    protected function nextColumn() 
    {
    }

}

?>
