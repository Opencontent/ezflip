<?php

if ( !$isQuiet )
{
    $cli->output( "Starting processing ezflip re-convert" );
}

$flip_dir_base = ezFlip::flipBaseFolderPath();
$dirList = array();
if ( $handle = @opendir( $flip_dir_base ) )
{
    while ( ( $file = readdir( $handle ) ) !== false )
    {
        if ( ( $file == "." ) || ( $file == ".." ) )
        {
            continue;
        }
        if ( is_dir( $flip_dir_base . '/' . $file ) )
        {
            $dirList[] = array( 'path' => $flip_dir_base, 'name' => $file, 'type' => 'dir' );            
        }        
    }
    @closedir( $handle );
}

$db = eZDB::instance();
$flipINI = eZINI::instance( 'ezflip.ini' );
$classIdentifiers = $flipINI->variable( 'FlipConvertAll', 'Classes' );
$attributeIdentifiers = $flipINI->variable( 'FlipConvertAll', 'Attributes' );

eZContentObject::clearCache();

foreach ( $dirList as $dirValue )
{
    $object = eZContentObject::fetch( $dirValue['name'] );
    
    if ( !$object )
    {
        continue;
    }
    
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
                $cli->output( 'Add to ezflip pending list: ' . $object->attribute('name') );
            }
        }
    }
    
}


if ( !$isQuiet )
{
    $cli->output( "Done" );
}

?>