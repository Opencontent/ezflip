<?php

class FlipMegazine implements FlipHandlerInterface
{
    /**
     * @var eZINI
     */
    public $FlipINI;

    /**
     * @var eZINI
     */
    public $SiteINI;

    /**
     * @var eZContentObjectAttribute
     */
    public $attribute;

    /**
     * @var array
     */
    public $files = array();

    /**
     * @var bool
     */
    public $generateContentObjectImages;

    /**
     * @var string
     */
    public $flipObjectDirectory;

    /**
     * @var bool|eZCLI
     */
    protected $cli;

    /**
     * @var bool
     */
    protected $isConverted;

    /**
     * @var string
     */
    protected $flipVarDirectory;

    /**
     * @var string
     */
    protected $flipObjectDirectoryName;

    /**
     * @param eZContentObjectAttribute $attribute
     * @param bool $useCli
     * @throws Exception
     */
    public function __construct( eZContentObjectAttribute $attribute, $useCli = false )
    {
        $this->SiteINI = eZINI::instance();
        $this->FlipINI = eZINI::instance( 'ezflip.ini' );

        if( $useCli )
        {
            $this->checkDependencies();
        }

        if ( !$attribute instanceof eZContentObjectAttribute )
        {
            throw new Exception( "Object isn't a eZContentObjectAttribute" );
        }

        if ( !$attribute->attribute( 'has_content' ) )
        {
            throw new Exception( "Attribute is empty" );
        }

        if ( $attribute->attribute( 'data_type_string' ) != 'ezbinaryfile' )
        {
            throw new Exception( "Attribute isn't a ezbinaryfile" );
        }

        if ( $attribute->attribute( 'content' )->attribute( 'mime_type' ) != 'application/pdf' )
        {
            throw new Exception( "File isn't a PDF file" );
        }

        $this->cli = $useCli ? eZCLI::instance() : false;

        $this->attribute = $attribute;

        $this->generateContentObjectImages = $this->FlipINI->variable( 'FlipSettings', 'GenerateContentObjectImages' ) == 'enabled';

        $this->flipVarDirectory = $this->SiteINI->variable( 'FileSettings','VarDir' ) . '/storage/original/application_flip';
        //@todo make flip folder versioned
        //$this->flipObjectDirectoryName = $this->attribute->attribute( 'id' ) . '-' . $this->attribute->attribute( 'version' );
        $this->flipObjectDirectoryName = $this->attribute->attribute( 'id' );
        $this->flipObjectDirectory = $this->flipVarDirectory . '/' . $this->flipObjectDirectoryName;
        $this->readFiles();
        return $this;
    }

    public function checkDependencies()
    {
        FlipMegazinePdfHelper::instance()->checkDependencies();
    }

    /**
     * Return the relative directory used by flip_dir template operator
     * This is a workaround to MegaZine3
     * @see generateSymLink
     * @return string
     */
    public function getFlipData()
    {
        $ini = eZINI::instance( 'site.ini' );
        $varDir = $ini->variable( 'FileSettings','VarDir' );
        return '../../../../../../../flip/get/' . basename( $varDir ) . '/' . $this->attribute->attribute( 'id' ) . '/' . $this->attribute->attribute( 'version' );
    }

    /**
     * @deprecated deprecated since version 2.0
     */
    public function getFlipDirectory()
    {
        return $this->getFlipData();
    }

    /**
     * Create the object directory in flip var dir
     * Call the FlipMegazinePdfHelper to split pdf in images in the object flip var dir
     * @return FlipMegazine
     * @throws Exception
     */
    protected  function preparePdf()
    {
        eZDir::recursiveDelete( $this->flipObjectDirectory );
        eZDir::mkdir( $this->flipObjectDirectory, false, true );
        if ( !is_dir( $this->flipObjectDirectory ) )
        {
            throw new Exception( 'Can not create directory ' . $this->flipObjectDirectory );
        }

        $storedFile = $this->attribute->storedFileInformation( false, false, false );
        $storedFilePath = $this->SiteINI->variable( 'FileSettings','VarDir' ) . '/' . $storedFile['filepath'];
        FlipMegazinePdfHelper::instance()->splitPDFPages( $this->flipObjectDirectory, $storedFilePath );
        $this->readFiles();        
        return $this;
    }

	protected function readFiles()
	{
        $fileList = array();
        eZDir::recursiveList( $this->flipObjectDirectory, $this->flipObjectDirectory, $fileList );
        foreach( $fileList as $item )
        {
            if ( $item['type'] == 'file' && eZFile::suffix( $item['name'] ) == 'pdf' && !in_array( $item['name'], $this->files ) )
            {
                $this->files[$item['name']] = $item['name'];
            }
        }
        ksort( $this->files );
    }

    /**
     * @return FlipMegazine
     * @throws Exception
     */
    protected function createImages()
    {
        if ( $this->generateContentObjectImages )
        {
            FlipMegazineImageHelper::deleteThumb( $this->attribute->attribute( 'object' )->attribute( 'main_node_id' ), $this->cli );
        }
        $sizes = $this->FlipINI->variable( 'FlipSettings', 'SizeThumb');
        $sizesOptions = $this->FlipINI->variable( 'FlipSettings', 'SizeThumbOptions');
        
        $i = 0;
        foreach( $this->files as $file )
        {
            $i++;
            foreach ( $sizes as $size )
            {
                $options = '';
                if ( isset( $sizesOptions[$size] ) )
                {
                    $options = $sizesOptions[$size];
                }

                $pageName = self::generatePageFileName( $i, $size );
                FlipMegazinePdfHelper::instance()->createImageFromPDF( $size, $this->flipObjectDirectory, $file, $pageName, $options );

                $ratio = getimagesize( $this->flipObjectDirectory . '/' . $pageName );
                if ( !is_array( $ratio ) )
                {
                    throw new Exception( 'failed creating ' . $pageName );
                }

                if ( $this->generateContentObjectImages && $size == 'large' )
                {
                    FlipMegazineImageHelper::createThumb( $this->flipObjectDirectory,
                                                     $pageName,
                                                     $this->attribute->attribute( 'object' )->attribute( 'main_node_id' ) );
                }
            }
        }
        $this->deletePDFFiles();
        return $this;
    }

    protected function deletePDFFiles()
    {
        foreach( $this->files as $fileName )
        {
            $file = eZClusterFileHandler::instance( $this->flipObjectDirectory . "/" . $fileName );
            if ( $file->exists() )
            {
                $file->delete();
            }
        }
    }

    public static function generatePageFileName( $index, $size, $suffix = null )
    {
        if ( $suffix === null )
        {
            $suffix = FlipMegazinePdfHelper::instance()->flipBookPageImageSuffix();
        }
        return "page" . sprintf( "%04d", $index ) . "_" . $size . "." . $suffix;
    }

    protected function createBook()
    {
        $sizes = $this->FlipINI->variable( 'FlipSettings', 'SizeThumb');
        $books = $this->FlipINI->variable( 'FlipBookSettings', 'FlipBook');
        foreach ( $books as $book )
        {
            $args = $this->FlipINI->variable( 'FlipBookSettings_' . $book, 'FlipBookSettings_' . $book);
            $ratio = getimagesize( $this->flipObjectDirectory . '/' . self::generatePageFileName( 1, $sizes['large'] ) );
            if ( !is_array( $ratio ) )
            {
                throw new Exception( 'getimagesize return wrong value' );
            }
            $ratio = $ratio[1] / $ratio[0];
            $args['ratio'] = $ratio;

            $xml = FlipMegazineXmlHelper::openBook( $args );
            $i = 0;
            foreach ( $this->files as $file)
            {
                $i++;
                $xml .= FlipMegazineXmlHelper::writePage(
                    $i,
                    $sizes[$args['thumb_size']],
                    $sizes[$args['full_size']],
                    $this->getFlipDirectory()
                );
            }
            $xml .= FlipMegazineXmlHelper::closeBook();
            eZFile::create( "magazine_" . $book . ".xml", $this->flipObjectDirectory, $xml );
        }
        return $this;
    }

	public function convert()
    {
        $this->preparePdf()
            ->createImages()
            ->createBook();

        eZContentCacheManager::clearObjectViewCache( $this->attribute->attribute( 'contentobject_id' ) );

		return true;
    }

    /**
     * Check if user has flipd.
     *
     * @return bool
     */
    public function isConverted()
    {
		$books = $this->FlipINI->variable( 'FlipBookSettings', 'FlipBook');
		foreach ( $books as $book )
        {
            $file = eZClusterFileHandler::instance( $this->flipObjectDirectory . "/magazine_" . $book . ".xml" );
            if ( $file->exists() )
            {
                return true;
            }
        }
        eZDebug::writeNotice( 'File ' . $this->flipObjectDirectory . "/magazine_" . $book . ".xml" . ' not found' );
        return false;

    }

    /*
     * Read the page dimensions from xml book description
     *
     * @var string $bookName
     *
     * @return bool
     */
    public function getPageDimensions( $bookName )
    {
		$books = $this->FlipINI->variable( 'FlipBookSettings', 'FlipBook');
		foreach ( $books as $book )
        {
            if ( $book == $bookName )
            {
                $file = eZClusterFileHandler::instance( $this->flipObjectDirectory . "/magazine_" . $book . ".xml" );
                if ( $file->exists() )
                {
                    $xml = simplexml_load_file( $file->filePath );
                    $width = $xml['pagewidth'];
                    $height = $xml['pageheight'] + 100;
                    return array( $width, $height );
                }
            }
        }
        return false;
    }
    
    public function getFlipFileInfo( $fileName )
    {
        $info = array();
        $suffix = eZFile::suffix( $fileName );
        $fileNameWithoutSuffix = str_replace( $suffix, '', $fileName );
        switch ( $suffix )
        {
            case 'xml':
            {
                $info['header'] = "Content-Type:text/xml";
            } break;

            default:
            {
                $data = FlipMegazinePdfHelper::instance()->flipImageInfo( $fileName );
                if ( !$data )
                {
                    throw new Exception( "File format $suffix not handled $fileName" );
                }
                $info['header'] = $data['header'];
                $suffix = $data['suffix'];
            }
        }
        $info['path'] = $this->flipObjectDirectory . '/'. $fileNameWithoutSuffix . $suffix;
        $info['content'] = file_get_contents( $info['path'] );
        return $info;
    }

    public function template()
    {
        return 'design:ezflip/megazine.tpl';
    }
}

?>
