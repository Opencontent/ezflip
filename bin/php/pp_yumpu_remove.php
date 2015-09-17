<?php
require 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => ( "Remove yumpu data from storage" ),
                                     'use-session' => false,
                                     'use-modules' => true,
                                     'use-extensions' => true ) );

$script->startup();

$options = $script->getOptions(
    '[attribute-id:][attribute-version:]',
    '',
    array(
        'attribute-id' => 'ezbinary attribute id',        
        'attribute-version' => 'ezbinary attribute version'   
    )
);
$script->initialize();
$script->setUseDebugAccumulators( true );

$user = eZUser::fetchByName( 'admin' );
eZUser::setCurrentlyLoggedInUser( $user , $user->attribute( 'contentobject_id' ) );

if ( empty( $options['attribute-id'] )     
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
        print_r( $flip->getFlipData() );
        if ( $flip->removeFlipData() )
        {
            $cli->warning( "Done" );
        }
    }
}

$script->shutdown();