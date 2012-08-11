<?php

class ezFlip
{
	
	public function ezFlip(){}
	
    public static function flipFolderPath ( $object_id = 0 ) 
	{
		if ( $object_id == 0 )
		{
			eZDebug::writeError( 'No object_id found', __METHOD__ );
			return false;
		}		
		$ini = eZINI::instance( 'site.ini' );
		$var = $ini->variable( 'FileSettings','VarDir' );
		$flip_dir = $var . '/storage/original/application_flip/' . $object_id;
		eZDebug::writeNotice( 'flip_dir: ' . $flip_dir . ' ' . var_export( file_exists( $flip_dir ), 1 ), __METHOD__ );
		return $flip_dir;
	}
	
	public static function readFlipFolder ( $flipFolder, $onlyPdf = false ) 
	{
		if ($handle = opendir($flipFolder )) {
			$files = array();
			while ( false !== ($file = readdir($handle)) ) {
				if  ( ($file!=='.') && ($file!=='..') && ($file!=='doc_data.txt') ) {
					if ( $onlyPdf )
					{
						if ( substr( $file, -4) == '.pdf' )
						{
							$k = intval( substr($file, 3, 4) );
							$files[$k]=$file;
						}
					}
					else
					{
						$k = intval( substr($file, 3, 4) );
						$files[$k]=$file;
					}
				}
			}
			asort($files);
			closedir($handle);
			
			if ( count( $files ) )
			{
				eZDebug::writeNotice( 'letti i file in flip_dir: ' . $files[1]  , __METHOD__ );
				return $files;
			}
			else
			{
				eZDebug::writeError( 'errore in lettura file'  , __METHOD__ );
				return $files;
			}
		}
		return false;
    }
	
	// esegue tutto il processo di conversione
	public static function convert( $args, $cli = false )
    {	
		$currentUser = eZUser::currentUser();
        if ( $cli )
        {
            $user = eZUser::fetchByName( 'admin' );
            if ( !$user )
            {
                $user = eZUser::currentUser();
            }
            eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ) );
        }
        
        $ini = eZINI::instance( 'ezflip.ini' );		
		
		$ret = self::checkArgs( $args, $cli );
        if ( $cli )
            $cli->output( 'Controllo le configurazioni' );
		if ( !empty( $ret['errors'] ) )
			return $ret;

        if ( $cli )
            $cli->output( 'Preparo il file PDF' );		
		$files = self::preparePdf( $args, $cli );

		if ( isset( $files['errors'] ) )
        {
            if ( $cli )
            {
                foreach( $files['errors'] as $error  )
                {
                    $cli->output( $error );
                }
            }
			return $files;
        }

		foreach ($files as $k => $file) 
		{
            if ( $cli )
    			$cli->output( 'Creo immagine ' . $k . ' di ' . count( $files ) );
            self::createImage( $k, $file, $ret );
		} 
        
        if ( count( $files ) )
        {
            if ( $cli )
                $cli->output( 'Creo i file XML' );
            self::createBook( $files, $ret );
        }
        
        if ( $cli )
            $cli->output( 'Svuoto la cache' );
        eZContentCacheManager::clearObjectViewCache( $ret['object_id'] );
        
        eZUser::setCurrentlyLoggedInUser( $currentUser, $currentUser->attribute( 'contentobject_id' ) );

		return true;
    }
	
	public static function createBook( $files, $ret )
	{
		$ini = eZINI::instance( 'ezflip.ini' );
		$sizes = $ini->variable( 'FlipSettings', 'SizeThumb');
		$sizesOptions = $ini->variable( 'FlipSettings', 'SizeThumbOptions');		
		$flip_dir = self::flipFolderPath ( $ret['object_id'] );
		
		$books = $ini->variable( 'FlipBookSettings', 'FlipBook');		

		foreach ( $books as $book )
		{
			$args = $ini->variable( 'FlipBookSettings_' . $book, 'FlipBookSettings_' . $book);
			
			// calcolo le proporzioni della pagina
			$ratio = getimagesize( $flip_dir . "/page" . sprintf('%04d', 1) .  "_" .  $sizes['large'].".jpg" );
			$ratio = $ratio[1] / $ratio[0];
			$args['ratio'] = $ratio;
			
			$xml = eZFlipXmlHandler::writeBookOpen( $args );
			foreach ($files as $k => $file) 
			{
				$xml .= eZFlipXmlHandler::writePage( $k, $sizes[$args['thumb_size']], $sizes[$args['full_size']], $file, $ret['object_id'] );
			}			
			$xml .= eZFlipXmlHandler::writeBookClose();
			if ( !eZFlipXmlHandler::createXml( $book, $xml, $ret['object_id'], $flip_dir) )
				eZDebug::writeError( 'Error ceating XML!', __METHOD__ );
		}
		return true;
	}
	
	public static function createImage( $k, $file, $ret )
	{
		
		$ini = eZINI::instance( 'ezflip.ini' );
		$sizes = $ini->variable( 'FlipSettings', 'SizeThumb');
		$sizesOptions = $ini->variable( 'FlipSettings', 'SizeThumbOptions');		
		$flip_dir = self::flipFolderPath ( $ret['object_id'] );
		$contentObject = $ret['contentObject'];
		
		if ( !is_object( $contentObject ) )
		{
			eZDebug::writeError( 'non-object', __METHOD__ );
			return false;
		}	
		
		foreach ( $sizes as $key => $size )
		{
			$options='';
			if ( isset( $sizesOptions[$size] ) )
				$options = $sizesOptions[$size];
            if ( isset( $sizesOptions[$key] ) )
                $options = $sizesOptions[$key];
			
			$page_name = "page" . sprintf("%04d", $k) . "_" . $size . ".jpg";
			eZFlipPdfHandler::createImageFromPDF($size, $flip_dir, $file, $page_name, $options);
		}			
		return eZFlipImageHandler::createThumb( $flip_dir . "/", "page" . sprintf("%04d", $k) . "_" . $sizes['large'] . ".jpg", $contentObject->attribute('main_node_id'), "Pagina ".$k );		 
	}
	
	public static function checkArgs( $args, $cli = false )
	{	
		$ret = array( 
			'attribute_id' => 0, 
			'object_id' => 0, 
			'already_flipped' => false, 
			'stats' => false,
			'contentObject' => false,
			'errors' => array()
		);
		
		if ( isset( $args[3] ) )
			$ret['node_id'] = $args[3];
			
		if ( isset( $args[2] ) )
			$ret['object_id'] = $args[2];
			
		if ( isset( $args[1] ) )
			$ret['contentobject_version'] = $args[1];
			
		if ( isset( $args[0] ) )
			$ret['attribute_id'] = $args[0];
			
		if ( !isset( $args[2] ) || !is_numeric( $args[0] ) || !is_numeric( $args[1] ) || !is_numeric( $args[2] ) )
		{
			$ret['errors'][] = 'attribute_id, contentobject_version, object_id not found';
			eZDebug::writeError( 'attribute_id, contentobject_version, object_id not found', __METHOD__ );
			return $ret;	
		}

		// Provide extra session protection on 4.1 (not possible on 4.0) by expecting user
		// to have an existing session (new session = mostlikely a spammer / hacker trying to manipulate rating)
		if ( !$cli && class_exists( 'eZSession' ) && eZSession::userHasSessionCookie() !== true )
		{		
			$ret['errors'][] = 'session protection';
			eZDebug::writeError( 'session protection', __METHOD__ );
			return $ret;	
		}
		// Return if parameters are not valid attribute id + version numbers
		$contentobjectAttribute = eZContentObjectAttribute::fetch( $ret['attribute_id'], $ret['contentobject_version'] );
		if ( !$contentobjectAttribute instanceof eZContentObjectAttribute )
		{		
			$ret['errors'][] = 'not valid attribute id';
			eZDebug::writeError( 'not valid attribute id', __METHOD__ );
			return $ret;	
		}

		// Return if attribute is not a file attribute
		if ( $contentobjectAttribute->attribute('data_type_string') !== eZBinaryFileType::DATA_TYPE_STRING )
		{		
			$ret['errors'][] = 'attribute is not a file attribute';
			eZDebug::writeError( 'attribute is not a file attribute', __METHOD__ );
			return $ret;	
		}

		// Return if user does not have access to object
		$contentobject = $contentobjectAttribute->attribute('object');
		if ( !$contentobject instanceof eZContentObject || !$contentobject->attribute('can_read') )
		{		
			$ret['errors'][] = 'not have access to object';
			eZDebug::writeError( 'not have access to object', __METHOD__ );
			return $ret;	
		}
		
		$ret['contentObject'] = eZContentObject::fetch( (int) $ret['object_id'] );
		
		eZDebug::writeNotice( 'argomenti letti correttamente' , __METHOD__ );
		
		return $ret;	
	}
	
	// esegue il primo step di conversione e restituisce i file pdf splittati
	public static function preparePdf( $args, $cli = false )
	{		
		$ret = self::checkArgs( $args, $cli );

		if ( !empty( $ret['errors'] ) )
			return $ret;
	
		$contentObject = $ret['contentObject'];
		
		$flip_dir = self::flipFolderPath ( $ret['object_id'] );
		
		eZDir::recursiveDelete( $flip_dir );			
		$success = eZDir::mkdir( $flip_dir, false, true );
        if ( !$success )
            return array( 'errors' => array( 'Impossibile creare la cartella ' . $flip_dir ) );
		
		$ini = eZINI::instance( 'site.ini' );
		$var = $ini->variable( 'FileSettings','VarDir' );
		$storedFile = eZFlipPdfHandler::getPDFFile( $contentObject );
		$storedFilePath = $var . '/' . $storedFile['filepath'];
        
        if ( $cli )
            $cli->output( 'Separo le pagine del file PDF' );
		eZFlipPdfHandler::splitPDFPages( $flip_dir, $storedFilePath, $cli );	
		
		$files = self::readFlipFolder( $flip_dir );		
		
		// cancella le immagini precedentemente generate
        if ( $cli )
            $cli->output( 'Elimino le immagini precedentemente create in node #' . $contentObject->attribute('main_node_id')  );
		eZFlipImageHandler::deleteThumb( $contentObject->attribute('main_node_id'), $cli );
		
		return $files;	
	}

	
    /**
     * Check if user has flipd.
     *
     * @param array $args ( 0 => contentobject_id,  1 => contentobjectattribute_id )
     * @return bool|null (null if params are wrong)
     */
    public static function has_converted( $object_id )
    {
        $file = self::flipFolderPath( $object_id );
        $ini = eZINI::instance( 'ezflip.ini' );		
		$books = $ini->variable( 'FlipBookSettings', 'FlipBook');		
		foreach ( $books as $book )
        {
            $file = eZClusterFileHandler::instance( $file . "/magazine_" . $book . ".xml" );
            if ( $file->exists() )
            {
                return true;
            }
        }
        return false;

    }

    public static function get_page_dimensions( $object_id, $bookName )
    {
        $file = self::flipFolderPath( $object_id );
        $ini = eZINI::instance( 'ezflip.ini' );		
		$books = $ini->variable( 'FlipBookSettings', 'FlipBook' );		
		foreach ( $books as $book )
        {
            if ( $book == $bookName )
            {
                $file = $file . "/magazine_" . $book . ".xml";
                $iniBook = $ini->variable( 'FlipBookSettings_' . $book, 'FlipBookSettings_' . $book );
                $fileObject = eZClusterFileHandler::instance( $file );
                if ( $fileObject->exists() )
                {
                    $xml = simplexml_load_file( $file );
                    $pagewidth = $xml['pagewidth'];
                    $pageheight = $xml['pageheight'];
                    if ( isset( $iniBook['navigation'] ) && $iniBook['navigation'] !== 'false' )
                    {
                        $pageheight = $pageheight + 100;
                    }
                    else
                    {
                        $pageheight = $pageheight + 20;
                    }
                    return array( $pagewidth, $pageheight );
                }
            }
        }
        return false;

    }
    
    public static function createFirstPagePreview( $object )
    {
        $attributeImage = false;
        $attributeFile = false;
        $dataMap = $object->attribute( 'data_map' );
        foreach ( $dataMap as $attribute )
        {
            if ( $attribute->attribute( 'data_type_string' ) == 'ezimage' )
            {
                $attributeImage = $attribute;
                eZDebug::writeNotice( "Attribute image found", __METHOD__ );
            }
            elseif (
                 $attribute->attribute( 'data_type_string' ) == 'ezbinaryfile'
                 && $attribute->attribute( 'has_content' )
                 && $attribute->attribute( 'content' )->attribute( 'mime_type' ) == 'application/pdf'
                )
            {
                $attributeFile = $attribute;
                eZDebug::writeNotice( "Attribute file found", __METHOD__ );
            }
            else
            {
                continue;
            }
        }
        
        if ( $attributeFile && $attributeImage )
        {
            $source = $attributeFile->attribute( 'content' )->attribute( 'filepath' );
            $page = 0;
            $width = 800;
            $height = 800;
            $pdffile = eZClusterFileHandler::instance( $attributeFile->attribute( 'content' )->attribute( 'filepath' ) );
            if ( !$pdffile->exists() )
            {
                eZDebug::writeError( "File not readable or doesn't exist", __METHOD__ );
                return false;;
            }
            $filename = urlencode( $object->attribute( 'name' ) ) . '.png';
            $dirPath = eZSys::cacheDirectory();
            $target = "$dirPath/$filename";
            if ( !file_exists( $target ) )
            {
                $fileHandler = eZClusterFileHandler::instance( $target );
                $pdffile->fetch(true);
                $cmd =  "nice -n 19 convert -colorspace RGB -density 600 " . eZSys::escapeShellArgument( $source . "[" . $page . "]" ) . " " . "-resize " . eZSys::escapeShellArgument(  $width . "x" . $height . ">" ) . " " . eZSys::escapeShellArgument( $target );
                $out = shell_exec( $cmd );
                $fileHandler = eZClusterFileHandler::instance();
                $fileHandler->fileStore( $target, 'pdfpreview-image', false );
                eZDebug::writeDebug( $cmd, "pdfpreview" );
                if ( $out )
                {
                    eZDebug::writeDebug( $out, "pdfpreview" );
                }
                $attributeImage->fromString( $target . "|" . $object->attribute( 'name' ) );
                $db = eZDB::instance();
                $db->begin();
                $attributeImage->store();
                $db->commit();
                
                eZContentCacheManager::clearObjectViewCache( $object->attribute( 'id' ) );
                return true;
            }
        }
        return false;
    }

}

?>
