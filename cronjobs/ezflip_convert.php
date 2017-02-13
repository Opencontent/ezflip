<?php

$cli->setIsQuiet( $isQuiet );
$cli->output( "Starting processing ezflip convert" );

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

do
{
    eZContentObject::clearCache();
    /** @var eZPendingActions[] $entries */
    $entries = eZPersistentObject::fetchObjectList( eZPendingActions::definition(),  null, $filterConds, null, array( 'limit' => $limit, 'offset' => $offset ) );

    if ( is_array( $entries ) && count( $entries ) != 0 )
    {
        foreach ( $entries as $entry )
        {
            $args = unserialize( $entry->attribute( 'param' ) );
            try
            {
                eZFlip::convert( $args, $cli );
                $entry->remove();
            }                
            catch( RuntimeException $e )
            {
                $cli->output( $e->getMessage() );
            }
            catch( InvalidArgumentException $e )
            {
                $entry->remove();
                $cli->output( $e->getMessage() );
            }
            catch( Exception $e )
            {
                $cli->error( $e->getMessage() );                
            }
        }
    }
    $limit['offset'] += $length;

} while ( count( $entries ) == $length );


if ( !$isQuiet )
{
    $cli->output( "Done" );
}

?>