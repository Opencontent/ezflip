<?php

interface FlipMegazineHelperInterface
{
    function checkDependencies();

    function flipBookPageImageSuffix();

    function createImageFromPDF ( $size, $filePath, $fileName, $pageName, $options );

    function splitPDFPages( $directory, $fileName );

    function flipImageInfo( $fileName );
}