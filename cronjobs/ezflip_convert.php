<?php

if ( !$isQuiet )
{
    $cli->output( "Starting processing ezflip convert" );
}

$contentObjects = array();
$db = eZDB::instance();

$offset = 0;
$limit = 50;
$action = 'ezflip_convert';
$filterConds = array( 'action' => $action );
$count = count( eZPendingActions::fetchByAction( $action ) );
$cli->output( 'There are ' .  $count  . ' pending items.' );
$length = 50;
$limit = array( 'offset' => 0 , 'length' => $length );

$script->resetIteration( $count );

try
{
    do
    {
        eZContentObject::clearCache();
        $entries = eZPersistentObject::fetchObjectList( eZPendingActions::definition(),  null, $filterConds, null, array( 'limit' => $limit, 'offset' => $offset ) );

        if ( is_array( $entries ) && count( $entries ) != 0 )
        {
            foreach ( $entries as $entry )
            {
                $args = unserialize( $entry->attribute( 'param' ) );
                eZFlip::convert( $args, $cli );
                $entry->remove();
            }
        }
        $limit['offset'] += $length;

    } while ( count( $entries ) == $length );
}
catch( Exception $e )
{
    $cli->output( $e->getMessage() );
}

if ( !$isQuiet )
{
    $cli->output( "Done" );
}

?>