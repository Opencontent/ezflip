<?php

abstract class PdftkBaseHelper implements FlipMegazineHelperInterface
{
    protected static function isCli()
    {
        return !eZCLI::instance()->isWebOutput();
    }

    public function flipImageInfo( $fileName )
    {
        $suffix = eZFile::suffix( $fileName );
        switch ( $suffix )
        {
            case 'jpg_':
            case 'jpg':
            {
                return array(
                    'header' => 'Content-Type: image/jpeg',
                    'suffix' => 'jpg'
                );
            }

            case 'image-png':            
            {
                return array(
                    'header' => 'Content-Type: image/png',
                    'suffix' => $suffix
                );
            }
        }
        return false;
    }

    public function checkDependencies()
    {
        $pdftkExecutable = eZINI::instance( 'ezflip.ini' )->variable( 'HelperSettings', 'PdftkExecutablePath' );
        $gsExecutable = eZINI::instance( 'ezflip.ini' )->variable( 'HelperSettings', 'GhostscriptExecutablePath' );

        $command = "{$pdftkExecutable} --version";
        exec( $command, $resultPdftk );
        if ( empty( $resultPdftk )  )
        {
            throw new Exception( 'pdftk not found. Install it from http://www.pdflabs.com/tools/pdftk-the-pdf-toolkit/' );
        }

        $command = "{$gsExecutable} --version";
        exec( $command, $resultGs );
        if ( empty( $resultGs ) )
        {
            throw new Exception( 'Ghostscript not found.' );
        }
    }

    public function splitPDFPages( $directory, $fileName )
    {
        $pdftkExecutable = eZINI::instance( 'ezflip.ini' )->variable( 'HelperSettings', 'PdftkExecutablePath' );
        $prePdftkCommand = '';
        if ( eZINI::instance( 'ezflip.ini' )->hasVariable( 'HelperSettings', 'PdftkPreParameters' ) )
        {
            $prePdftkCommand = trim( eZINI::instance( 'ezflip.ini' )->variable( 'HelperSettings', 'PdftkPreParameters' ) ) . ' ';
        }
        $directory = self::isCli() ? $directory : eZSys::rootDir() . eZSys::fileSeparator() . $directory;
        $fileName = self::isCli() ? '../../application/' . basename( $fileName ) : eZSys::rootDir() . eZSys::fileSeparator() . $fileName;
        $command = 'cd '. $directory . ';' . $prePdftkCommand . $pdftkExecutable . ' ' . $fileName . ' burst';
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