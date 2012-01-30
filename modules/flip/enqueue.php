<?php

$http = eZHTTPTool::instance();
$module = $Params["Module"];
$tpl = eZTemplate::factory();
$errors = array();

$attribute_id = $Params['AttributeID'];
$contentobject_version = $Params['ContentobjectVersion'];
$object_id = $Params['ObjectID'];
$node_id = $Params['NodeID'];
$reflip = isset( $Params['Reflip'] ) ? intval( $Params['Reflip'] ) : 0;

$node = eZContentObjectTreeNode::fetch( $node_id );
if ( !$node instanceof eZContentObjectTreeNode )
{		
    return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );	
}

$contentobjectAttribute = eZContentObjectAttribute::fetch( $attribute_id, $contentobject_version );
if ( !$contentobjectAttribute instanceof eZContentObjectAttribute )
{		
    return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );	
}

$do_convert = true;
if ( ezFlip::has_converted( $object_id ) )
{
    if ( $reflip == 2 )
    {
        $do_convert = true;
    }
    else
    {
        $errors[] = 'Il file &egrave; gi&agrave; stato convertito';
        $do_convert = false;
    }
}

if ( $do_convert )
{
    $args = array( $attribute_id, $contentobject_version, $object_id, $node_id  );
    
    $exist = eZPendingActions::fetchObject( eZPendingActions::definition(), null, array( 'param' => serialize( $args ) ) );
    
    if ( !$exist )
    {
        $pending = new eZPendingActions( array(
            'action' => 'ezflip_convert',
            'created' => time(),
            'param' => 	serialize( $args )
        ) );
        $pending->store();
        $reflip = 0;
    }
    else
    {
        $errors[] = 'Il file &egrave; in elaborazione';
        $reflip = 0;
    }
}

$tpl->setVariable( 'reflip', $reflip );
$tpl->setVariable( 'attribute', $contentobjectAttribute );
$tpl->setVariable( 'node', $node );

$tpl->setVariable( 'attribute_id', $attribute_id );
$tpl->setVariable( 'contentobject_version', $contentobject_version );
$tpl->setVariable( 'object_id', $object_id );
$tpl->setVariable( 'node_id', $node_id );

$tpl->setVariable( 'errors', $errors );

/*
if ( isset( $args[3] ) )
			$ret['node_id'] = $args[3];
			
		if ( isset( $args[2] ) )
			$ret['object_id'] = $args[2];
			
		if ( isset( $args[1] ) )
			$ret['contentobject_version'] = $args[1];
			
		if ( isset( $args[0] ) )
			$ret['attribute_id'] = $args[0];
*/

$Result = array();
$Result['content'] = $tpl->fetch( "design:ezflip/enqueue.tpl" );


?>
