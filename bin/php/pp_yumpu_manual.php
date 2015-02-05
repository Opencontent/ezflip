<?php
require 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => ( "Add yumpu data to file" ),
                                     'use-session' => false,
                                     'use-modules' => true,
                                     'use-extensions' => true ) );

$script->startup();

$options = $script->getOptions(
    '[attribute-id:][attribute-version:][public-yumpu-id:][private-yumpu-id:]',
    '',
    array(
        'attribute-id' => 'ezbinary attribute id',        
        'attribute-version' => 'ezbinary attribute version',        
        'public-yumpu-id' => 'public yumpu item id',        
        'private-yumpu-id' => 'private yumpu item id',        
    )
);
$script->initialize();
$script->setUseDebugAccumulators( true );

$user = eZUser::fetchByName( 'admin' );
eZUser::setCurrentlyLoggedInUser( $user , $user->attribute( 'contentobject_id' ) );

if ( empty( $options['attribute-id'] )
     || empty( $options['public-yumpu-id'] )
     || empty( $options['private-yumpu-id'] )
     || empty( $options['attribute-version'] ) )
{
    $cli->error( "Specify all options" );
}
else
{    
    $contentObjectAttribute = eZContentObjectAttribute::fetch( $options['attribute-id'] , $options['attribute-version']  );
    if ( !$contentObjectAttribute instanceof eZContentObjectAttribute )
    {
        return false;
    }
    else
    {
        $flip = eZFlip::instance( $contentObjectAttribute );
        if ( $flip instanceof PublicPrivatePremiumFlipYumpu )
        {
            $flip->addManual( $options['public-yumpu-id'], $options['private-yumpu-id'] );
        }
    }
}

$script->shutdown();