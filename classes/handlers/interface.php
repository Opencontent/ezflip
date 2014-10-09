<?php

interface FlipHandlerInterface
{
    /**
     * @param eZContentObjectAttribute $attribute
     * @param bool $useCli
     */
    function __construct( eZContentObjectAttribute $attribute, $useCli );

    /**
     * @return bool
     */
    function isConverted();

    /**
     * @param $bookIdentifier
     * @return array( $width, $height )
     */
    function getPageDimensions( $bookIdentifier );

    /**
     * @return string
     */
    function getFlipData();

    /**
     * @param $filename
     * @return array associative array of header, path, content
     */
    function getFlipFileInfo( $filename );

    /**
     * @return bool
     */
    function convert();


    /**
     * @return string
     */
    function template();
}