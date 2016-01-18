<?php

if ( !$isQuiet )
{
    $cli->output( "Starting processing search all ezflip convertible objects" );
}

$contentObjects = array();
$db = eZDB::instance();
$flipINI = eZINI::instance( 'ezflip.ini' );
$classIdentifiers = $flipINI->variable( 'FlipConvertAll', 'Classes' );
$attributeIdentifiers = $flipINI->variable( 'FlipConvertAll', 'Attributes' );

$classes = array();

foreach( $classIdentifiers as $identifer )
{
    $class = eZContentClass::fetchByIdentifier( $identifer );
    if ( $class )
    {
        $classes[] = $class->attribute( 'id' );
    }
}

$def = eZContentObject::definition();
$conds = array(
    'contentclass_id' => array( $classes ),
    'status' => eZContentObject::STATUS_PUBLISHED
);

$count = eZPersistentObject::count( $def, $conds, 'id' );

$cli->output( "Number of objects: $count");
$found = 0;
$length = 50;
$limit = array( 'offset' => 0 , 'length' => $length );

$script->resetIteration( $count );

do
{
    // clear in-memory object cache
    eZContentObject::clearCache();

    $objects = eZPersistentObject::fetchObjectList( $def, null, $conds, null, $limit );

    foreach ( $objects as $object )
    {
        $dataMap = $object->dataMap();
        foreach( $attributeIdentifiers as $identifer )
        {
            if ( isset( $dataMap[$identifer] ) )
            {
                $attribute = $dataMap[$identifer];
                
                $attribute_id = $attribute->attribute( 'id' );
                $contentobject_version = $attribute->attribute( 'version' );
                $object_id = $object->attribute( 'id' );
                $node_id = $object->attribute( 'main_node_id' );
                
                $args = array( $attribute_id, $contentobject_version, $object_id, $node_id  );    
                $exist = eZPendingActions::fetchObject( eZPendingActions::definition(), null, array( 'param' => serialize( $args ) ) );
                
                if ( !$exist )
                {
                    sleep(1); //rallento lo script sennò il time non mi permette di inserire  contenuti
                    $pending = new eZPendingActions( array(
                        'action' => 'ezflip_convert',
                        'created' => time(),
                        'param' => 	serialize( $args )
                    ) );
                    $pending->store();
                    $found++;
                }
            }
        }

        $script->iterate( $cli, true );
    }

    $limit['offset'] += $length;

} while ( count( $objects ) == $length );

if ( !$isQuiet )
{
    $cli->output( "Found $found objects to convert" );
}

?>