<?php
$Module = array( 'name' => 'eZ Flip' );

$ViewList = array();

$ViewList['enqueue'] = array(
    'script' => 'enqueue.php',
    'functions' => array( 'enqueue' ),
    'ui_context' => 'navigation',
    'params' => array( 'ContentObjectAttributeID', 'ContentObjectVersion','ReFlip' )
);
						 
$FunctionList = array();
$FunctionList['enqueue'] = array();

?>
