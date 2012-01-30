<?php

class eZFlipImageHandler
{

    public function eZFlipImageHandler() {}

    public static function deleteThumb ( $parent_node_id, $cli )
    {
		$removeNodeIdList = array();
		$contentTree = eZContentObjectTreeNode::fetch( $parent_node_id );
		$children = eZContentObjectTreeNode::subTreeByNodeID(  array( 'ClassFilterType' => 'include', 'ClassFilterArray' => array( 'image' ) ), $parent_node_id );
        if ( $cli )
            $cli->output( "Trovate " . count( $children ) . " immagini" );
		foreach ( $children as $i => $node )
        {
			$node->removeNodeFromTree();
		}
    }

    public static function createThumb ($filepath, $imagename, $parent_node_id, $image_name )
    {
		/*
		$user = eZUser::fetchByName( 'admin' );
		if (!$user){//if no user exists let's pull out the current user:
			$user = eZUser::currentUser();
		}
		eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ) );
		*/
		$user = eZUser::currentUser();
		$ini = eZINI::instance( 'ezflip.ini' );		
		
		//setting node details
		$params = array();
		$params['class_identifier'] = 'image';
		$params['creator_id'] = $user->ContentObjectID; //using the user extracted above
		$params['parent_node_id']=$parent_node_id; //pulling the node id out of the parent 
		$params['storage_dir'] = eZSys::rootDir() . eZSys::fileSeparator() . $filepath;
		/*required so ez knows where to look. The ending "/" required. $_SERVER['pwd'] is being used as the script is being run through the command line. I’ve created the folder “import_images” on the server and moved my image into it.*/

		//setting attribute values
		$attributesData = array ( ) ;
		$attributesData['name'] = $image_name; 
		$attributesData['image'] = $imagename; 

		//storing xml content for the caption
##TODO Undefined index: #text in /var/www/ez/kernel/classes/datatypes/ezxmltext/ezxmlinputparser.php on line 1116 e Undefined index: section in /var/www/ez/kernel/classes/datatypes/ezxmltext/ezxmlinputparser.php on line 1116
/*		
		$XMLContent = "<p>immagine generata dal pdf</p>";
		$parser = new eZXMLInputParser();
		$parser->setParseLineBreaks( true );
		$document = $parser->process( $XMLContent );
		$xmlString = eZXMLTextType::domString( $document );
		$attributesData['caption'] = $xmlString;
*/
		$params['attributes'] = $attributesData;

		//publishing node
		$imageObject = eZContentFunctions::createAndPublishObject($params);
		return $imageObject;

	}
}
?>
