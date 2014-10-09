<?php

class FlipMegazinePdfHelper
{

    /**
     * @return FlipMegazineHelperInterface
     */
    public static function instance()
    {
        $flipINI = eZINI::instance( 'ezflip.ini' );
        if ( $flipINI->hasVariable( 'HelperSettings', 'PdfHelperClass' ) )
        {
            $className = $flipINI->variable( 'HelperSettings', 'PdfHelperClass' );
        }
        else
        {
            $className = 'PdftkImagikHelper';
        }
        return new $className();
    }

}
?>
