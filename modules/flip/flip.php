<?php

include_once( "kernel/common/template.php" );

$http = eZHTTPTool::instance();
$module = $Params["Module"];
$tpl = eZTemplate::factory();
$errors = array();
$node_id = $Params['NodeID'];

$node = eZContentObjectTreeNode::fetch($node_id);

if ( !$node )
{
    return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}

$tpl->setVariable('node', $node);
$tpl->setVariable('object', $node->object());
$tpl->setVariable('errors', $errors);

$Result = array();
$Result['content'] = $tpl->fetch( "design:ezflip/flip.tpl" );


?>
