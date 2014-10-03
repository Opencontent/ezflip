<?php

class PdftkImagikHandler extends PdftkBaseHandler
{
    public function flipBookPageImageSuffix()
    {
        return 'image-jpg';
    }

    public function createImageFromPDF ( $size, $filePath, $fileName, $pageName, $options = '' )
    {
        $preConvertCommand = '';
        if ( eZINI::instance( 'ezflip.ini' )->hasVariable( 'HelperSettings', 'ConvertPreParameters' ) )
        {
            $preConvertCommand = trim( eZINI::instance( 'ezflip.ini' )->variable( 'HelperSettings', 'ConvertPreParameters' ) ) . ' ';
        }
        $filePath = eZSys::rootDir() . eZSys::fileSeparator() . $filePath;
        $command = "{$preConvertCommand}convert " . $options . " -resize " . $size . " -colorspace RGB " . $filePath . "/" . $fileName . " " . $filePath . "/" . $pageName;
        if ( self::isCli() && !eZCLI::instance()->isQuiet() )
        {
            eZCLI::instance()->output( $command );
        }
        else
        {
            eZDebug::writeNotice( $command , __METHOD__ );
        }
        $result = shell_exec( $command );
        return $result;
    }

}