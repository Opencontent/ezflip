<?php

class eZFlipTemplateOperators
{
    function operatorList()
    {
        return array( 'flip_exists', 'get_page_dimensions', 'flip_dir', 'flip_data', 'flip_template' );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array(
            'flip_exists' => array(
                'id' => array( 'type' => 'integer', 'required' => true ),
                'version' => array( 'type' => 'integer', 'required' => true )
            ),
            'get_page_dimensions' => array(
                'id' => array( 'type' => 'integer', 'required' => true ),
                'version' => array( 'type' => 'integer', 'required' => true ),
                'view' => array( 'type' => 'string', 'required' => false, 'default' => 'large' )
            ),
            'flip_data' => array(
                'id' => array( 'type' => 'integer', 'required' => true ),
                'version' => array( 'type' => 'integer', 'required' => true )
            ),
            'flip_dir' => array(
                'id' => array( 'type' => 'integer', 'required' => true ),
                'version' => array( 'type' => 'integer', 'required' => true )
            ),
            'flip_template' => array(
                'id' => array( 'type' => 'integer', 'required' => true ),
                'version' => array( 'type' => 'integer', 'required' => true )
            ),
        );
                                              
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        $contentObjectAttribute = eZContentObjectAttribute::fetch( (int)$namedParameters['id'], (int)$namedParameters['version'] );
        if ( !$contentObjectAttribute instanceof eZContentObjectAttribute )
        {
            return false;
        }

        try
        {
            $flip = eZFlip::instance( $contentObjectAttribute );
            switch ( $operatorName )
            {
                case 'flip_exists':
                {
                    return $operatorValue = $flip->isConverted();
                } break;

                case 'get_page_dimensions':
                {
                    return $operatorValue = $flip->getPageDimensions( $namedParameters['view'] );
                } break;

                case 'flip_dir':
                case 'flip_data':
                {
                    return $operatorValue = $flip->getFlipData();
                } break;

                case 'flip_template':
                {
                    return $operatorValue = $flip->template();
                } break;
            }
        }
        catch( Exception $e )
        {
            eZDebug::writeError( $e->getMessage(), __METHOD__ );
        }

        return true;
    }
}
