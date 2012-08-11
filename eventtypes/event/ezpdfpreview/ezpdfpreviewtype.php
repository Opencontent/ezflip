<?php

class eZPdfPreviewType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = "ezpdfpreview";
    
	function eZPdfPreviewType()
    {
        $this->eZWorkflowEventType( eZPdfPreviewType::WORKFLOW_TYPE_STRING, ezpI18n::tr( 'opencontent', 'Create image preview form file PDF' ) );
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array( 'after' ) ) ) );
    }

    function execute( $process, $event )
    {
        $parameterList = $process->attribute( 'parameter_list' );
        $objectID = $parameterList['object_id'];
        $object = eZContentObject::fetch( $objectID );        
        
        if ( $object )
        {
            ezFlip::createFirstPagePreview( $object );
        }
        
        return eZWorkflowType::STATUS_ACCEPTED;
    }

}

eZWorkflowEventType::registerEventType( eZPdfPreviewType::WORKFLOW_TYPE_STRING, 'eZPdfPreviewType' );

?>