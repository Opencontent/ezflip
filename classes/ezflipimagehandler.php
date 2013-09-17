<?php

class eZFlipImageHandler
{

    public static function deleteThumb( $parentNodeId, $cli = false )
    {
		$children = eZContentObjectTreeNode::subTreeByNodeID(
            array( 'ClassFilterType' => 'include',
                   'ClassFilterArray' => array( 'image' ) ),
            $parentNodeId
        );

        if ( count( $children ) > 0 )
        {
            $message = "Remove " . count( $children ) . " images from node $parentNodeId";
            if ( $cli )
            {
                $cli->output( $message );
            }
            else
            {
                eZDebug::writeNotice( $message , __METHOD__ );
            }
            foreach ( $children as $node )
            {
                $node->removeNodeFromTree();
            }
        }

    }

    public static function createThumb( $directory, $imageName, $parentNodeId  )
    {
		$user = eZUser::currentUser();
		$params = array();
		$params['class_identifier'] = 'image';
		$params['creator_id'] = $user->attribute( 'contentobject_id' );
		$params['parent_node_id'] = $parentNodeId;
		$params['storage_dir'] = eZSys::rootDir() . eZSys::fileSeparator() . $directory . '/';
		$attributesData = array ( ) ;
		$attributesData['name'] = $imageName;
		$attributesData['image'] = $imageName;
		$params['attributes'] = $attributesData;
		$imageContentObject = eZContentFunctions::createAndPublishObject($params);
		return $imageContentObject;
	}
}
?>
