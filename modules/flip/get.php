<?php

$module = $Params["Module"];
$VarDir = $Params["VarDir"];
$ContentObjectAttributeID = (int) $Params["ContentObjectAttributeID"];
$ContentObjectVersion = (int) $Params["ContentObjectVersion"];
$FileName = $Params["FileName"];

$contentObjectAttribute = eZContentObjectAttribute::fetch( $ContentObjectAttributeID, $ContentObjectVersion );
if ( !$contentObjectAttribute instanceof eZContentObjectAttribute )
{
    eZDebugSetting::writeError( 'ezflip', "Attribute not found" );
    header("HTTP/1.0 404 Not Found");
    eZExecution::CleanExit();
}

if ( !$contentObjectAttribute->attribute( 'object' )->attribute( 'can_read' ) )
{
    eZDebugSetting::writeError( 'ezflip', "User does haven't permission to read object" );
    header("HTTP/1.0 403 Forbidden");
    eZExecution::CleanExit();
}

try
{
    $flip = eZFlip::instance( $contentObjectAttribute );
    $info = $flip->getFlipFileInfo( $FileName );
    header ( $info['header'] );        
    echo $info['content'];
}
catch( Exception $e )
{
    eZDebugSetting::writeError( 'ezflip', $e->getMessage() );
    header("HTTP/1.0 403 Forbidden");    
}
eZExecution::CleanExit();