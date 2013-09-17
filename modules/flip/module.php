<?php
$Module = array( 'name' => 'eZ Flip' );

$ViewList = array();

$ViewList['enqueue'] = array(
    'script' => 'enqueue.php',
    'functions' => array( 'enqueue' ),
    'ui_context' => 'navigation',
    'params' => array( 'ContentObjectAttributeID', 'ContentObjectVersion','ReFlip' )
);

$ViewList['get'] = array(
    'script' => 'get.php',
    'functions' => array( 'get' ),
    'ui_context' => 'navigation',
    'params' => array( 'VarDir', 'ContentObjectAttributeID', 'ContentObjectVersion', 'FileName' )
);
						 
$FunctionList = array();
$FunctionList['enqueue'] = array();
$FunctionList['get'] = array();

?>
