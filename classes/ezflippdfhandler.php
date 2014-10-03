<?php

class eZFlipPdfHandler
{

    /**
     * @return FlipHandlerInterface
     */
    public static function instance()
    {
        $flipINI = eZINI::instance( 'ezflip.ini' );
        if ( $flipINI->hasVariable( 'HelperSettings', 'PdfHandlerClass' ) )
        {
            $className = $flipINI->variable( 'HelperSettings', 'PdfHandlerClass' );
        }
        else
        {
            $className = 'PdftkImagikHandler';
        }
        return new $className();
    }

}
?>
