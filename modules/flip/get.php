<?php

$module = $Params["Module"];
$VarDir = $Params["VarDir"];
$ContentObjectAttributeID = (int) $Params["ContentObjectAttributeID"];
$ContentObjectVersion = (int) $Params["ContentObjectVersion"];
$FileName = $Params["FileName"];

$contentObjectAttribute = eZContentObjectAttribute::fetch( $ContentObjectAttributeID, $ContentObjectVersion );
if ( !$contentObjectAttribute instanceof eZContentObjectAttribute )
{
    eZDebug::writeError( "Attribute not found" );
    header("HTTP/1.0 404 Not Found");
    eZExecution::CleanExit();
}

if ( !$contentObjectAttribute->attribute( 'object' )->attribute( 'can_read' ) )
{
    eZDebug::writeError( "User does haven't permission to read object" );
    header("HTTP/1.0 403 Forbidden");
    eZExecution::CleanExit();
}

try
{
    $eZFlip = new eZFlip( $contentObjectAttribute );
    $info = $eZFlip->getFlipFileInfo( $FileName );
    header ( $info['header'] );    
    echo $info['content'];
}
catch( Exception $e )
{
    eZDebug::writeError( $e->getMessage() );
    header("HTTP/1.0 403 Forbidden");    
}
eZExecution::CleanExit();