<?php

class FlipMegazineCompat extends FlipMegazine
{
    public function isConverted()
    {
        if ( !parent::isConverted() ){
            return $this->setCompatMode();
        }
        return parent::isConverted();
    }

    public function getFlipFileInfo( $fileName )
    {
        $this->setCompatMode();
        return parent::getFlipFileInfo( $fileName );
    }

    public function setCompatMode(){
        $objectNamedDirectoryName = $this->attribute->attribute( 'contentobject_id' );
        $objectNamedDirectory = $this->flipVarDirectory . '/' . $objectNamedDirectoryName;
        if ( file_exists( $objectNamedDirectory ) ){
            $this->flipObjectDirectoryName = $objectNamedDirectoryName;
            $this->flipObjectDirectory = $this->flipVarDirectory . '/' . $this->flipObjectDirectoryName;
            $this->pdfHelper = new PdftkImagikHelper();
            $this->readFiles();
            eZDebug::writeNotice( 'Found compat folder ' . $this->flipObjectDirectory );
        }
        return false;
    }

}