<?php

class eZFlipType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = "ezflip";
    
	function __construct()
    {
        parent::__construct( eZFlipType::WORKFLOW_TYPE_STRING, ezpI18n::tr( 'opencontent', 'Flip File PDF' ) );
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array( 'after' ) ) ) );
    }

    function execute( $process, $event )
    {
        $parameterList = $process->attribute( 'parameter_list' );
        $objectID = $parameterList['object_id'];
        $object = eZContentObject::fetch( $objectID );        
        $flipINI = eZINI::instance( 'ezflip.ini' );
        $classIdentifiers = $flipINI->variable( 'FlipConvertAll', 'Classes' );
        $attributeIdentifiers = $flipINI->variable( 'FlipConvertAll', 'Attributes' );
        
        if ( in_array( $object->attribute( 'class_identifier' ), $classIdentifiers ) )
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
                    
                    try
                    {
                        $flip = eZFlip::instance($attribute);
                        if ( !$exist && !$flip->isConverted() ) 
                        {
                            sleep(1); //rallento lo script sennò il time non mi permette di inserire  contenuti
                            $pending = new eZPendingActions( array(
                                'action' => 'ezflip_convert',
                                'created' => time(),
                                'param' => 	serialize( $args )
                            ) );
                            $pending->store();
                        }                        
                    }
                    catch( Exception $e )
                    {
                        eZDebugSetting::writeError( 'ezflip', $e->getMessage(), __METHOD__ );
                    }
                }
            }
        }
        return eZWorkflowType::STATUS_ACCEPTED;
    }

}

eZWorkflowEventType::registerEventType( eZFlipType::WORKFLOW_TYPE_STRING, 'eZFlipType' );

?>
