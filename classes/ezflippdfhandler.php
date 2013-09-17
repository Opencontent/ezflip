<?php

class eZFlipPdfHandler
{
    public static function createImageFromPDF ( $size, $filePath, $fileName, $pageName, $options = '', $cli = false )
	{
        $preConvertCommand = '';
        if ( eZINI::instance( 'ezflip.ini' )->hasVariable( 'HelperSettings', 'ConvertPreParameters' ) )
        {
            $preConvertCommand = trim( eZINI::instance( 'ezflip.ini' )->variable( 'HelperSettings', 'ConvertPreParameters' ) ) . ' ';
        }
        $filePath = eZSys::rootDir() . eZSys::fileSeparator() . $filePath;
        $command = "{$preConvertCommand}convert " . $options . " -resize " . $size . " -colorspace RGB " . $filePath . "/" . $fileName . " " . $filePath . "/" . $pageName;
        if ( $cli )
        {
            $cli->output( $command );
        }
        else
        {
            eZDebug::writeNotice( $command , __METHOD__ );
        }
        $result = shell_exec( $command );
        return $result;
    }

    public static function splitPDFPages( $directory, $fileName, $cli = false )
	{
        $pdftkExecutable = eZINI::instance( 'ezflip.ini' )->variable( 'HelperSettings', 'PdftkExecutablePath' );
        $preConvertCommand = '';
        if ( eZINI::instance( 'ezflip.ini' )->hasVariable( 'HelperSettings', 'PdftkPreParameters' ) )
        {
            $prePdftkCommand = trim( eZINI::instance( 'ezflip.ini' )->variable( 'HelperSettings', 'PdftkPreParameters' ) ) . ' ';
        }
        $directory = $cli ? $directory : eZSys::rootDir() . eZSys::fileSeparator() . $directory;
        $fileName = $cli ? '../../application/' . basename( $fileName ) : eZSys::rootDir() . eZSys::fileSeparator() . $fileName;
		$command = 'cd '. $directory . ';' . $prePdftkCommand . $pdftkExecutable . ' ' . $fileName . ' burst';
        if ( $cli )
        {
    		$cli->output( $command );
        }
        else
        {
            eZDebug::writeNotice( $command , __METHOD__ );
        }
		$result = shell_exec( $command );
		return $result;
    }
}
?>
