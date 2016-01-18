<?php

class eZFlipPdfHandler
{

    public function eZFlipPdfHandler(){}

    public static function createImageFromPDF ( $size, $file_path, $filename, $page_name, $options='' ) 
	{
		$command = "nice -n 19 convert " . $options . " -resize " . $size . " -colorspace RGB ".$file_path."/".$filename." ".$file_path."/" . $page_name;
		
		eZDebug::writeNotice( 'converto ' . $filename . ' alle dimensioni di ' . $size  , __METHOD__ );
		
		system($command);
    }

    public static function getPDFFile ( $contentObject ) 
	{
		$dataMap = $contentObject->attribute( 'data_map' );
		$file_pdf = $dataMap['file'];
		$storedFile = $file_pdf->storedFileInformation( false, false, false );
		
		eZDebug::writeNotice( 'leggo il file ' . $storedFile['filepath'] . ' di tipo ' . $storedFile['mime_type']  , __METHOD__ );
		
		return $storedFile;
    }
	

    public static function splitPDFPages( $folder, $filename, $cli = false )
	{
		/*
		$user = eZUser::fetchByName( 'admin' );
		if (!$user){//if no user exists let's pull out the current user:
				$user = eZUser::currentUser();
		}
		eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ) );		
		*/
		$folder = $cli ? $folder : eZSys::rootDir() . eZSys::fileSeparator() . $folder;
		$filename = $cli ? '../../application/' . basename( $filename ) : eZSys::rootDir() . eZSys::fileSeparator() . $filename;
		$command = 'cd '. $folder . '; nice -n 19 pdftk ' . $filename . ' burst';
		
        if ( $cli )
        {
    		$cli->output( 'Eseguo: cd '. $folder . '; nice -n 19 pdftk ' .$filename . ' burst' );		        
        }
        else
        {
    		eZDebug::writeNotice( 'eseguo l\'istruzione: cd '. $folder . '; pdftk ' .$filename . ' burst'  , __METHOD__ );		
        }
		
		$result = shell_exec( $command );
		
		eZDebug::writeNotice( $result  , __METHOD__ );		
		
		return $result;
    }
	
}
?>
