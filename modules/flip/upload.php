<?php
/**
 * File containing the eZ Publish upload view implementation.
 *
 * @copyright Copyright (C) 1999-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/gnu_gpl GNU GPL v2
 * @version 1.0.0
 * @package ezmultiupload
 */

include_once( 'kernel/common/template.php' );

$http = eZHTTPTool::instance();
$tpl = eZTemplate::factory();
$module = $Params['Module'];
$parentNodeID = $Params['ParentNodeID'];

// Check if current action is an real upload action
if( $module->isCurrentAction( 'Upload' ) )
{
	$result = array( 'errors' => array() );


    // Handle file upload only if there was no errors
    if( count( $result['errors'] ) == 0 )
    {
        // Handle file upload. All checkes are performed by eZContentUpload::handleUpload()
        // and available in $result array
        $upload = new eZContentUpload();
        $upload->handleUpload( $result, 'Filedata', $parentNodeID, false );
    }
	
    // Pass result to template and process it
    $tpl->setVariable( 'result', $result );
    $templateOutput = $tpl->fetch( 'design:ezflip/thumbnail.tpl' );

	$meta = array( 'errors' => false );
	if( count( $result['errors'] ) == 0 )
	{
		$node = $result['contentobject_main_node'];
		$dataMap = $node->dataMap();
		$file_id = $dataMap['file']->attribute('id');	
		$vars = array(
			$file_id,
			1, # TODO versioning
			$result['contentobject_id']
		);
		$meta['vars'] =	$vars;
		/*
		$files = ezFlip::preparePdf( $vars );		
		
		if ( isset( $files['errors'] ) )
			$meta['errors'] = $files['errors'];
		else
			$meta['files'] = count($files);
		*/
	}
	else
	{
		$meta['errors'] = $result['errors'];
	}
 
    $response = array( 'data' => $templateOutput, 'meta' => $meta );

    // Return server response in JSON format
    echo json_encode( $response );

    // Stop execution
    eZExecution::cleanExit();
}
else
{
    // Check if parent node ID provided in URL exists and is an integer
    if ( !$parentNodeID || !is_numeric( $parentNodeID ) )
        return $module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

    // Fetch parent node
    $parentNode = eZContentObjectTreeNode::fetch( $parentNodeID );
    
    // Check if parent node object exists
    if( !$parentNode )
        return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );

    // Check if current user has access to parent node and can create content inside
    if( !$parentNode->attribute( 'can_read' ) || !$parentNode->attribute( 'can_create' ) )
        return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );

    // Get configuration INI settings for ezmultiupload extension
    $uploadINI = eZINI::instance( 'ezflip.ini' );
    $availableClasses = $uploadINI->variable( 'FlipSettings', 'AvailableClasses' );
    $availableSubtreeList = $uploadINI->variable( 'FlipSettings', 'AvailableSubtreeNode' );
    $parentNodeClassIdentifier = $parentNode->attribute( 'class_identifier' );

    // Check if current parent node class identifier and node ID match configuration settings
    if( !in_array( $parentNodeClassIdentifier, $availableClasses )
            && !in_array( $parentNodeID, $availableSubtreeList ) )
        return $module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );

    $availableFileTypes = array();
    $availableFileTypesStr = '';

    // Check if file types setting is available for current subtree
    if( $uploadINI->hasGroup( 'FileTypeSettings_' . $parentNodeID ) )
        $availableFileTypes = $uploadINI->variable( 'FileTypeSettings_' . $parentNodeID, 'FileType' );

    // Check if file types setting is available for current class identifier
    // and merge it with previusly loaded settings
    if( $uploadINI->hasGroup( 'FileTypeSettings_' . $parentNodeClassIdentifier ) )
        $availableFileTypes = array_merge( $availableFileTypes, $uploadINI->variable( 'FileTypeSettings_' . $parentNodeClassIdentifier, 'FileType' ) );

    // Create string with available file types for GUI uploader
    if ( count( $availableFileTypes ) > 0 )
        $availableFileTypesStr = implode( ';', $availableFileTypes );

    // Pass variables to upload.tpl template
    $tpl->setVariable( 'file_types', $availableFileTypesStr );
    $tpl->setVariable( 'session_id', session_id() );
    $tpl->setVariable( 'session_name', session_name() );
    $tpl->setVariable( 'user_session_hash', eZSession::getUserSessionHash() );
    $tpl->setVariable( 'parent_node', $parentNode );

    // Process template and set path data
    $Result = array();
    $Result['content'] = $tpl->fetch( 'design:ezflip/upload.tpl' );
    $Result['path'] = array( array( 'url' => false,
                                    'text' => ezpI18n::tr( 'extension/ezflip', 'Pdf Upload' ) ) );
}

?>
