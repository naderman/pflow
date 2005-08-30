<?php

require_once 'PHPUnit2/Framework/TestCase.php';

class ezcTestCase extends PHPUnit2_Framework_TestCase
{
    public function __construct( $string = "" )
    {
        parent::__construct( $string );
    }
}


?>
