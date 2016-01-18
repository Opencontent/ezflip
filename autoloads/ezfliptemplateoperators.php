<?php

class ezflipTemplateOperators
{
    function ezflipTemplateOperators()
    {
    }

    function operatorList()
    {
        return array( 'flip_exists', 'get_page_dimensions', 'symlink_flip_dir' );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array(
            'flip_exists' => array(
                'id' => array( 'type' => 'integer', 'required' => true, 'default' => 0 )
            ),
            'get_page_dimensions' => array(
                'id' => array( 'type' => 'integer', 'required' => true, 'default' => 0 ),
                'book' => array( 'type' => 'string', 'required' => true, 'default' => 'null' )
            )
        );
                                              
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        switch ( $operatorName )
        {
            case 'flip_exists':
            {
                return $operatorValue = ezFlip::has_converted( $namedParameters['id'] );
            } break;
            case 'get_page_dimensions':
            {
                return $operatorValue = ezFlip::get_page_dimensions( $namedParameters['id'], $namedParameters['book']  );
            } break;
            case 'symlink_flip_dir':
            {
               return $operatorValue = ezFlip::get_symlink_flip_dir(); 
            }
        }
        $operatorValue = $ret;
    }
}

?>
