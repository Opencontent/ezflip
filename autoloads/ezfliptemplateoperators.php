<?php

class eZFlipTemplateOperators
{
    function eZFlipTemplateOperators()
    {
    }

    function operatorList()
    {
        return array( 'flip_exists', 'get_page_dimensions', 'flip_dir' );
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
                'book' => array( 'type' => 'string', 'required' => true, 'default' => 'large' )
            ),
            'flip_dir' => array(
                'id' => array( 'type' => 'integer', 'required' => true ),
                'version' => array( 'type' => 'integer', 'required' => true )
            )
        );
                                              
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        $contentObjectAttribute = eZContentObjectAttribute::fetch( $namedParameters['id'], $namedParameters['version'] );
        if ( !$contentObjectAttribute instanceof eZContentObjectAttribute )
        {
            return false;
        }

        try
        {
            $eZFlip = new eZFlip( $contentObjectAttribute );
            switch ( $operatorName )
            {
                case 'flip_exists':
                {
                    return $operatorValue = $eZFlip->isConverted();
                } break;
                case 'get_page_dimensions':
                {
                    return $operatorValue = $eZFlip->getPageDimensions( $namedParameters['book'] );
                } break;
                case 'flip_dir':
                {
                    return $operatorValue = $eZFlip->getFlipDirectory();
                }
            }
        }
        catch( Exception $e )
        {
            eZDebug::writeError( $e->getMessage(), __METHOD__ );
            return false;
        }
    }
}

?>
