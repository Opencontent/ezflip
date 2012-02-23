<?php
$Module = array( 'name' => 'eZ Flip' );

$ViewList = array();
$ViewList['flip'] = array('script' => 'flip.php',
                          'functions' => array( 'flip' ),
						  'ui_context' => 'navigation',
						  'default_navigation_part' => 'ezflippart',
						  'params' => array( 'NodeID' )
						 );
$ViewList['enqueue'] = array('script' => 'enqueue.php',
                             'functions' => array( 'enqueue' ),
						  'ui_context' => 'navigation',
						  'params' => array( 'AttributeID', 'ContentobjectVersion', 'ObjectID', 'NodeID', 'Reflip' )
						 );
$ViewList['upload'] = array( 'script' => 'upload.php',
                             'functions' => array( 'upload' ),
                             'single_post_actions' => array( 'UploadButton' => 'Upload' ),
                             'params' => array( 'ParentNodeID' ) );
						 
$FunctionList = array();
$FunctionList['flip'] = array();
$FunctionList['enqueue'] = array();
$FunctionList['upload'] = array();

?>
