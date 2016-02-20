<?php

class FlipMegazineCompat extends FlipMegazine
{
    public function isConverted()
    {
        if ( !parent::isConverted() ){            
            $this->fixCompat();
        }
        return parent::isConverted();
    }

    protected function fixCompat(){
        $objectNamedDirectoryName = $this->attribute->attribute( 'contentobject_id' );
        $objectNamedDirectory = $this->flipVarDirectory . '/' . $objectNamedDirectoryName;
        if ( file_exists( $objectNamedDirectory ) ){
            eZDebug::writeNotice( 'Rename folder ' . $objectNamedDirectory . ' in ' . $this->flipObjectDirectory );
            rename( $objectNamedDirectory, $this->flipObjectDirectory );
            eZDebug::writeNotice( 'Fix books' );
            $this->fixCompatBooks();
            $this->readFiles();            
            return true;
        }
        return false;
    }
    
    protected function fixCompatBooks(){
        $books = (array)$this->FlipINI->variable( 'FlipBookSettings', 'FlipBook');
		foreach ( $books as $book )
        {
            $file = eZClusterFileHandler::instance( $this->flipObjectDirectory . "/magazine_" . $book . ".xml" );
            if ( $file->exists() )
            {
                $string = $file->fetchContents();
                $dom = new DOMDocument( '1.0', 'utf-8' );
                $success = $dom->loadXML( $string );
                if ( $success )
                {
                    $xpath = new DOMXPath( $dom );
                    foreach( $xpath->query( '//img' ) as $image ){
                        $src = $this->getFlipDirectory() . '/' . basename( $image->getAttribute('src') . '_' );
                        $hires = $this->getFlipDirectory() . '/' . basename( $image->getAttribute('hires') . '_' );
                        $image->setAttribute( 'src', $src );
                        $image->setAttribute( 'hires', $hires );
                        $data = $dom->saveXML();                        
                    }
                    eZFile::create( "magazine_" . $book . ".xml", $this->flipObjectDirectory, $data );
                }                
            }
        }
    }
    

}