<?php

class FlipYumpu implements FlipHandlerInterface
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
     * @var eZContentObject
     */
    public $object;

    /**
     * @var array
     */
    public $flipList = array();

    /**
     * @var eZClusterFileHandlerInterface
     */
    protected $flipListFile;

    /**
     * @var bool|eZCLI
     */
    protected $cli;

    /**
     * @param eZContentObjectAttribute $attribute
     * @param bool $useCli
     * @throws Exception
     */
    function __construct( eZContentObjectAttribute $attribute, $useCli = false )
    {
        $this->SiteINI = eZINI::instance();
        $this->FlipINI = eZINI::instance( 'ezflip.ini' );
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

        $this->attribute = $attribute;

        $this->object = $this->attribute->attribute( 'object' );

        $this->cli = $useCli ? eZCLI::instance() : false;

        $flipListFilePath = $this->SiteINI->variable( 'FileSettings','VarDir' ) . '/storage/original/application_flip/yumpu_converted.php';
        if ( !eZClusterFileHandler::instance( $flipListFilePath )->exists() )
        {
            eZClusterFileHandler::instance()->fileStore( $flipListFilePath );
        }
        $this->flipListFile = eZClusterFileHandler::instance( $flipListFilePath );

        $this->readFlipList();
    }

    /**
     * @return bool
     */
    function isConverted()
    {
        return isset( $this->flipList[$this->attribute->attribute( 'id' )] );
    }

    /**
     * @param $bookIdentifier
     * @return array( $width, $height )
     */
    function getPageDimensions( $bookIdentifier )
    {
        if ( isset( $this->flipList[$this->attribute->attribute( 'id' )] ) )
        {
            $data = $this->flipList[$this->attribute->attribute( 'id' )];
            return array( $data['document'][0]['width'], $data['document'][0]['height'] );
        }
        else
        {
            return array();
        }
    }

    /**
     * @return string
     */
    function getFlipData()
    {
        if ( isset( $this->flipList[$this->attribute->attribute( 'id' )] ) )
        {
            $data = $this->flipList[$this->attribute->attribute( 'id' )];

            //$id = $data['document'][0]['id'];
            //$connector = new YumpuConnector();
            //$connector->config['token'] = $this->FlipINI->variable( 'YumpuSettings', 'Token' );
            //$connector->config['debug'] = true;
            //$response = $connector->getDocument( array( 'id' => $id ) );

            foreach( $data['document'] as $i => $doc )
            {
                if ( isset( $doc['embed_code'] ) )
                {
                    $xml = new SimpleXMLElement( $doc['embed_code'] );
                    foreach( $xml->attributes() as $key => $value )
                    {
                        if ( $key == 'src' )
                        {
                            $data['document'][$i]['iframe_src'] = (string) $value;
                        }
                    }
                }
            }
            return $data;
        }
        else
        {
            return false;
        }
    }

    /**
     * @param $filename
     * @return array
     */
    function getFlipFileInfo( $filename )
    {
        eZDebug::writeError( "Yumpu handler can not show single page", __METHOD__ );
        return array();
    }

    /**
     * @return void     
     */
    function convert()
    {
        if ( isset( $this->flipList[$this->attribute->attribute( 'id' )] ) )
        {
            $data = $this->flipList[$this->attribute->attribute( 'id' )];
            if ( is_string( $data ) )
            {
                $this->getID( $data );
            }
            else
            {
                $this->updateFile( $data );
            }
        }
        else
        {
            $this->createFile();
        }
    }

    protected function getID( $progressId )
    {
        $connector = new YumpuConnector();
        $connector->config['token'] = $this->FlipINI->variable( 'YumpuSettings', 'Token' );
        $connector->config['debug'] = $this->FlipINI->variable( 'YumpuSettings', 'EnableDebug' );
        $response = $connector->getDocumentProgress( $progressId );
        if ( $response['state'] == 'success' )
        {
            $this->flipList[$this->attribute->attribute( 'id' )] = $response;
            $this->updateFlipList();
            eZContentCacheManager::clearObjectViewCache( $this->attribute->attribute( 'contentobject_id' ) );
        }
        else
        {
            throw new RuntimeException( "Conversion in progress" );
        }
    }

    protected function updateFile( $fileData )
    {
        $connector = new YumpuConnector();
        $connector->config['token'] = $this->FlipINI->variable( 'YumpuSettings', 'Token' );
        $connector->config['debug'] = $this->FlipINI->variable( 'YumpuSettings', 'EnableDebug' );
        $response = $connector->deleteDocument( $fileData['id'] );
        if ( $response['state'] != 'success' )
        {
            throw new Exception( "Error deleting {$fileData['id']}" );
        }
        $this->createFile();
    }

    protected function createFile()
    {
        $storedFile = $this->attribute->storedFileInformation( false, false, false );
        $filePath = eZSys::rootDir() . '/' . $storedFile['filepath'];

        $title = $this->object->attribute( 'name' );
        
        $currentLanguageParts = explode( '-', eZLocale::instance( $this->object->attribute( 'current_language' ) )->attribute( 'http_locale_code' ) );
        $language = strtolower( $currentLanguageParts[0] );
        if ( !$language )
        {
            throw new Exception( "Language not found" );
        }

        $description = $tags = false;
        if ( $this->FlipINI->hasVariable( 'YumpuSettings', 'DescriptionAttributeIdentifier' ) && isset( $dataMap[$this->FlipINI->variable( 'YumpuSettings', 'DescriptionAttributeIdentifier' )] ))
        {
            $attribute = $dataMap[$this->FlipINI->variable( 'YumpuSettings', 'DescriptionAttributeIdentifier' )];
            if ( $attribute->attribute( 'has_content' ) )
            {
                $description = strip_tags( $attribute->toString() );
            }
        }
        if ( $this->FlipINI->hasVariable( 'YumpuSettings', 'TagAttributeIdentifier' ) && isset( $dataMap[$this->FlipINI->variable( 'YumpuSettings', 'TagAttributeIdentifier' )] ))
        {
            $attribute = $dataMap[$this->FlipINI->variable( 'YumpuSettings', 'TagAttributeIdentifier' )];
            if ( $attribute->attribute( 'has_content' ) )
            {
                $tags = strip_tags( $attribute->toString() );
            }
        }

        $data = array(
            'file' => $filePath,
            'title' => $title,            
            'language' => $language,            
            'detect_elements' => 'y',            
        );
        
        if ( $description ) $data['description'] = $description;            
        if ( $tags ) $data['tags'] = $tags;

        $connector = new YumpuConnector();
        $connector->config['token'] = $this->FlipINI->variable( 'YumpuSettings', 'Token' );
        $connector->config['debug'] = $this->FlipINI->variable( 'YumpuSettings', 'EnableDebug' );
        $response = $connector->postDocumentFile( $data );
        if ( $response )
        {
            if ( isset( $response['progress_id'] ) )
            {
                $this->flipList[$this->attribute->attribute( 'id' )] = $response['progress_id'];
                $this->updateFlipList();
                throw new RuntimeException( "Waiting for remote conversion" );
            }
            else
            {
                throw new Exception( "Field 'progress_id' not found in yumpu response" );
            }
        }
        else
        {
            throw new Exception( "Conversion failed" );
        }
    }

    /**
     * @return string
     */
    function template()
    {
        return 'design:ezflip/yumpu.tpl';
    }

    protected function updateFlipList()
    {
        $this->flipListFile->storeContents( serialize( $this->flipList ) );
    }

    protected function readFlipList()
    {
        $this->flipList = unserialize( $this->flipListFile->fetchContents() );
    }
}