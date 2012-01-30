<?php
$Module = array( 'name' => 'eZFlip' );

$ViewList = array();
$ViewList['flip'] = array('script' => 'flip.php',
						  'ui_context' => 'navigation',
						  'default_navigation_part' => 'ezflippart',
						  'params' => array( 'NodeID' )
						 );
$ViewList['enqueue'] = array('script' => 'enqueue.php',
						  'ui_context' => 'navigation',
						  'params' => array( 'AttributeID', 'ContentobjectVersion', 'ObjectID', 'NodeID', 'Reflip' )
						 );
$ViewList['upload'] = array( 'script' => 'upload.php',
                             'single_post_actions' => array( 'UploadButton' => 'Upload' ),
                             'params' => array( 'ParentNodeID' ) );
						 
$FunctionList = array();
$FunctionList['flip'] = array();
$FunctionList['enqueue'] = array();
$FunctionList['upload'] = array();

?>
