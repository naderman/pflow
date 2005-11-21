<?php
/**
 * Example for the usage of eczConsoleProgressbar class.
 *
 * @package ConsoleTools
 * @version //autogen//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
 */

// ... creating ezcConsoleOutput object

// Set maximum value and step width for the progress bar ( using kb values )
$set = array( 'max' => $file->getSize(), 'step' => 50 );

// Some options to change the appearance
$opt = array(
    'emptyChar'     => '-',
    'progressChar'  => '#',
    'formatString'  => "Uploading file <{$myFilename}>: %act%/%max% kb [%bar%] %percent%%",
);

// Create progress bar itself
$progress = new ezcConsoleProgressbar( $out, $set, $opt );

// Do some actions
while( $file->upload() ) 
{
    // Advance the progressbar by one step ( uploading 5k per run )
    $progress->advance();
}

// Finish progress bar and jump to next line.
$progress->finish();

$out->outputText( "Successfully uploaded <{$myFilename}>.\n", 'success' );

/*
OUTPUT:

// At 30 %
Uploading file "ezpublish-4.0.0.tgz":  300/1000 kb [#####>--------------] 30%

// At 50 %
Uploading file "ezpublish-4.0.0.tgz":  500/1000 kb [#########>----------] 50%

// At 95 %
Uploading file "ezpublish-4.0.0.tgz":  950/1000 kb [###################>] 95%

// At 100 %
Uploading file "ezpublish-4.0.0.tgz": 1000/1000 kb [####################] 100%
Successfully uploaded "ezpublish-4.0.0.tgz".
*/
?>
