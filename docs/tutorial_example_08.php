<?php

require_once 'tutorial_autoload.php';

$output = new ezcConsoleOutput();

$output->formats->success->color = 'green';
$output->formats->success->style = array( 'bold' );

$output->formats->failure->color = 'red';
$output->formats->failure->style = array( 'bold' );

$bar = new ezcConsoleStatusbar( $output );

$bar->options->successChar = $output->formatText( '+', 'success' );
$bar->options->failureChar = $output->formatText( '-', 'failure' );

for ( $i = 0; $i < 1024; $i++ )
{
    $bar->advance();
    usleep(  mt_rand( 200, 2000 ) );
}

$bar->finish();

$output->outputLine();

?>
