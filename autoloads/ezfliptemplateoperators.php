<?php

class ezflipTemplateOperators
{
    function ezflipTemplateOperators()
    {
    }

    function operatorList()
    {
        return array( 'flip_exists' );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array( 'flip_exists' => array( 'id' => array( 'type' => 'integer',
                                              'required' => true,
                                              'default' => 0 ))
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
        }
        $operatorValue = $ret;
    }
}

?>
