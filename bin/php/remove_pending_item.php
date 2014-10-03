<?php
require 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => ( "Remove ezflip pending item" ),
                                     'use-session' => false,
                                     'use-modules' => true,
                                     'use-extensions' => true ) );

$script->startup();

$options = $script->getOptions(
    '[attribute-id:]',
    '',
    array(
        'attribute-id' => 'ezbinary attribute id'        
    )
);
$script->initialize();
$script->setUseDebugAccumulators( true );

$user = eZUser::fetchByName( 'admin' );
eZUser::setCurrentlyLoggedInUser( $user , $user->attribute( 'contentobject_id' ) );

if ( empty( $options['attribute-id'] ) )
{
    $cli->error( "Specify option 'attribute-id'" );
}
else
{
    $action = 'ezflip_convert';
    $filterConds = array( 'action' => $action );    
    $entries = eZPersistentObject::fetchObjectList( eZPendingActions::definition(),  null, $filterConds, null );
    if ( is_array( $entries ) && count( $entries ) != 0 )
    {
        foreach ( $entries as $entry )
        {
            $args = unserialize( $entry->attribute( 'param' ) );
            if ( $args[0] == $options['attribute-id'] )
            {
                $cli->output( "Remove ezflip pending item " . $entry->attribute( 'param' ) );
                $entry->remove();                
            }            
        }
    }
}

$script->shutdown();