<?php

require_once 'PHPUnit2/Framework/TestSuite.php';

class ezcTestSuite extends PHPUnit2_Framework_TestSuite
{
    public function __construct($theClass = '', $name = '')
    {
        parent::__construct($theClass, $name);
    }
    
}

?>
