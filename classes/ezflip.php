<?php

class eZFlip
{
    /**
     * @param eZContentObjectAttribute $attribute
     * @param bool $useCli
     * @return FlipHandlerInterface
     * @throws Exception
     */
    public static function instance( eZContentObjectAttribute $attribute, $useCli = false )
    {
        $flipINI = eZINI::instance( 'ezflip.ini' );
        $handlerClass = $flipINI->variable( 'HandlerSettings', 'HandlerClass' );
        if ( class_exists( $handlerClass ) )
        {
            $instance = new $handlerClass( $attribute, $useCli );
            if ( in_array( 'FlipHandlerInterface', class_implements( $instance ) ) )
            {
                return $instance;
            }
            else
            {
                throw new Exception( "Flip handler class must implement FlipHandlerInterface" );
            }
        }
        throw new Exception( "Flip handler class $handlerClass not found" );
    }

    /**
     * @param array|eZContentObjectAttribute $parameters
     * @param bool $useCli
     * @return bool
     * @throws Exception
     */
    public static function convert( $parameters, $useCli = false )
    {
        $contentObjectAttribute = null;
        if ( is_array( $parameters ) )
        {
            $contentObjectAttribute = eZContentObjectAttribute::fetch( $parameters[0], $parameters[1] );
        }
        elseif( $parameters instanceof eZContentObjectAttribute )
        {
            $contentObjectAttribute = $parameters;
        }

        if ( !$contentObjectAttribute instanceof eZContentObjectAttribute )
        {
            throw new Exception( 'Attribute not found' );
        }
        $flip = self::instance( $contentObjectAttribute, $useCli );
        return $flip->convert();
    }

}