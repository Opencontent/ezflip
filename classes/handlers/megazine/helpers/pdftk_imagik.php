<?php

class PdftkImagikHelper extends PdftkBaseHelper
{
    public function flipBookPageImageSuffix()
    {
        return 'jpg';
    }

    public function createImageFromPDF ( $size, $filePath, $fileName, $pageName, $options = '' )
    {
        $imageINI = eZINI::instance( 'image.ini' );
        if ( !$imageINI->variable( 'ImageMagick', 'IsEnabled' ) )
        {
            throw new Exception( "ImageMagick is disabled in image.ini" );
        }
        $preConvertCommand = '';
        if ( eZINI::instance( 'ezflip.ini' )->hasVariable( 'HelperSettings', 'ConvertPreParameters' ) )
        {
            $preConvertCommand = trim( eZINI::instance( 'ezflip.ini' )->variable( 'HelperSettings', 'ConvertPreParameters' ) ) . ' ';
        }
        $filePath = eZSys::rootDir() . eZSys::fileSeparator() . $filePath;
        $executableConvert = $imageINI->variable( 'ImageMagick', 'ExecutablePath' ) . '/' . $imageINI->variable( 'ImageMagick', 'Executable' );
        $command = "{$preConvertCommand}{$executableConvert}" . $options . " -resize " . $size . " -colorspace RGB " . $filePath . "/" . $fileName . " " . $filePath . "/" . $pageName;
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