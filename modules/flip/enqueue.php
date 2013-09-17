<?php

$http = eZHTTPTool::instance();
$module = $Params["Module"];
$tpl = eZTemplate::factory();
$errors = array();

$attributeId = (int) $Params['ContentObjectAttributeID'];
$contentObjectVersion = (int) $Params['ContentObjectVersion'];
$reFlip = isset( $Params['ReFlip'] ) ? intval( $Params['ReFlip'] ) : 0;

$contentObjectAttribute = eZContentObjectAttribute::fetch( $attributeId, $contentObjectVersion );
if ( !$contentObjectAttribute instanceof eZContentObjectAttribute )
{
    return $module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}

try
{
    $eZFlip = new eZFlip( $contentObjectAttribute );
    $doConvert = true;
    if ( $eZFlip->isConverted() )
    {
        if ( $reFlip == 2 )
        {
            $doConvert = true;
        }
        else
        {
            $errors[] = ezpI18n::tr( 'extension/ezflip', 'File already converted' );
            $doConvert = false;
        }
    }
    
    if ( $doConvert )
    {
        $args = serialize( array( $attributeId, $contentObjectVersion ) );
        
        $exist = eZPendingActions::fetchObject( eZPendingActions::definition(), null, array( 'param' => $args ) );
        
        if ( !$exist )
        {
            $pending = new eZPendingActions( array(
                'action' => 'ezflip_convert',
                'created' => time(),
                'param' => 	$args
            ) );
            $pending->store();
            $reFlip = 0;
        }
        else
        {
            $errors[] = ezpI18n::tr( 'extension/ezflip', 'The file is being processed' );
            $reFlip = 0;
        }
    }

    $tpl->setVariable( 'reflip', $reFlip );
    $tpl->setVariable( 'attribute', $contentObjectAttribute );
    $tpl->setVariable( 'errors', $errors );
    
    
    $Result = array();
    $Result['content'] = $tpl->fetch( "design:ezflip/enqueue.tpl" );
}
catch( RuntimeException $e )
{    
    $tpl->setVariable( 'commands', eZFlip::wizard() );
    $tpl->setVariable( 'error', $e->getMessage() );
    $tpl->setVariable( 'exception', $e->getTraceAsString() );
    $Result['content'] = $tpl->fetch( "design:ezflip/wizard.tpl" );
}
catch( Exception $e )
{
    $tpl->setVariable( 'error', $e->getMessage() );
    $tpl->setVariable( 'exception', $e->getTraceAsString() );
    $Result['content'] = $tpl->fetch( "design:ezflip/error.tpl" );
}

?>
