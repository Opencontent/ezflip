<?php

$module = $Params["Module"];
$VarDir = $Params["VarDir"];
$ContentObjectAttributeID = (int) $Params["ContentObjectAttributeID"];
$ContentObjectVersion = (int) $Params["ContentObjectVersion"];
$FileName = $Params["FileName"];

$contentObjectAttribute = eZContentObjectAttribute::fetch( $ContentObjectAttributeID, $ContentObjectVersion );
if ( !$contentObjectAttribute instanceof eZContentObjectAttribute )
{
    header("HTTP/1.0 404 Not Found");
    eZExecution::CleanExit();
}

if ( !$contentObjectAttribute->attribute( 'object' )->attribute( 'can_read' ) )
{
    header("HTTP/1.0 403 Forbidden");
    eZExecution::CleanExit();
}

try
{
    $eZFlip = new eZFlip( $contentObjectAttribute );
}
catch( Exception $e )
{
    header("HTTP/1.0 403 Forbidden");
    eZExecution::CleanExit();
}

$suffix = eZFile::suffix( $FileName );
if ( $suffix == 'xml' )
{
    header ("Content-Type:text/xml");
}
elseif ( $suffix == 'jpg' )
{
    header('Content-Type: image/jpeg');
}
else
{
    header("HTTP/1.0 403 Forbidden");
    eZExecution::CleanExit();
}

echo  file_get_contents( $eZFlip->flipObjectDirectory . '/'. $FileName );
eZExecution::CleanExit();
