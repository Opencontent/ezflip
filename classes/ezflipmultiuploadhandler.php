<?php

class ezflipmultiuploadhandler implements eZMultiuploadHandlerInterface
{
    static function preUpload( &$result )
	{
		return true;
	}
    static function postUpload( &$result )
	{								

		include_once( 'extension/ezflip/classes/ezflip.php' );
		include_once( 'extension/ezflip/classes/ezflipimagehandler.php' );
		include_once( 'extension/ezflip/classes/ezflippdfhandler.php' );
		include_once( 'extension/ezflip/classes/ezflipxmlhandler.php' );
		
		if ( !empty( $result ) )
		{
			if( count( $result['errors'] ) == 0 )
			{
				$node = $result['contentobject_main_node'];
				$dataMap = $node->dataMap();
				$file_id = $dataMap['file']->attribute('id');	
				$args = array(
					$file_id,
					1,
					$result['contentobject_id']
				);
				ezFlip::convert($args);
				#call_user_func( array( 'ezFlip', 'convert' ), $args );				
			}
		}
	}
}

?>