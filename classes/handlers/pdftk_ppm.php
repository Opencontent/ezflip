<?php

class PdftkPpmHandler extends PdftkBaseHandler
{
    public function flipBookPageImageSuffix()
    {
        return 'image-png';
    }

    public function checkDependencies()
    {
        parent::checkDependencies();
        
        $command = "pdfinfo -v 2>&1";
        $result = shell_exec( $command );
        if ( empty( $result )  )
        {
            throw new Exception( 'pdfinfo not found' );
        }
        
        $command = "pdftoppm -v 2>&1";
        $result = shell_exec( $command );
        if ( empty( $result )  )
        {
            throw new Exception( 'pdftoppm not found' );
        }
    }

    public function createImageFromPDF ( $size, $filePath, $fileName, $pageName, $options = '' )
    {
        // workaround
        //@see lists.freedesktop.org/archives/poppler-bugs/2011-November/007120.html
        //@see https://www.libreoffice.org/bugzilla/show_bug.cgi?id=43393

        $scaleWitdh = $size;

        $infoCommand = "pdfinfo {$filePath}/{$fileName}";
        $output = shell_exec( $infoCommand );
        if ( !empty( $output ) )
        {
            $data = array();
            $output = explode( "\n", $output );
            foreach( $output as $line )
            {
                $parts = explode( ":", $line );
                if ( isset( $parts[1] ) )
                {
                    $data[$parts[0]] = trim( $parts[1] );
                }
            }
            if ( isset( $data['Page size'] ) )
            {
                $widthHeight = explode( 'x', $data['Page size'] );
                if ( isset( $widthHeight[1] ) )
                {
                    $width = floatval( $widthHeight[0] );
                    $height = floatval( $widthHeight[1] );
                    $scaleHeight = intval( $scaleWitdh * $height / $width );
                    $command = "pdftoppm -png -r 72 -scale-to-x {$scaleWitdh} -scale-to-y {$scaleHeight} {$filePath}/{$fileName} {$filePath}/{$pageName}";
                    system( $command );
                    $destFile = "{$filePath}/{$pageName}";
                    $srcFile = "{$filePath}/{$pageName}-1.png";
                    return rename( $srcFile, $destFile );
                }
            }
        }
        return false;
    }
}