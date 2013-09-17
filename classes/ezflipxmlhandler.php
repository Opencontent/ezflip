<?php

class eZFlipXmlHandler
{

	public function eZFlipXmlHandler(){}
	
	
	public static function openBook ( $options = array() )
	{
		$defaultArgs = array(
			'lang' => 'it',
			'pagewidth' => 400,
			'pageheight' => 447,
			'zoominit' => 1000,
			'zoomcontrolalpha' => 0.5,
			'zoomrotate' => false,
			'navigation' => true,
		);
		
		$args = array_merge( $defaultArgs, $options );
		
		if ( isset( $args['ratio'] ) && ( $args['ratio'] > 0 ) )
        {
			$args['pageheight'] = round ( $args['pagewidth'] * $args['ratio'] );
        }
		
		$xml = "<?xml version='1.0' encoding='utf-8'?>
		<!DOCTYPE book SYSTEM 'http://megazine.mightypirates.de/megazine.dtd'>
		<book lang='" . $args['lang'] . "' pagewidth='" . $args['pagewidth'] . "' pageheight='" . $args['pageheight'] . "' zoominit='" . $args['zoominit'] . "' zoomrotate='" . $args['zoomrotate'] . "' zoomcontrolalpha='" . $args['zoomcontrolalpha'] . "' navigation='" . $args['navigation'] . "'>
			<chapter>";
			
		return $xml;
	
	}

	public static function writePage ( $index, $thumbSize, $fullSize, $directory, $options = array() )
	{
		$defaultArgs = array(
			'position' => 'bottom',
			'gallery' => 'Opencontent'
		);
		
		$args = array_merge( $defaultArgs, $options );

		$xml = "<page>
			<img position='" . $args['position'] . "' src='" . $directory . '/' .eZFlip::generatePageFileName( $index, $thumbSize) . "' hires='" . $directory . '/' . eZFlip::generatePageFileName( $index, $fullSize) . "' gallery='" . $args['gallery'] . "'/>
		</page>";
		
		return $xml;
	
	}

	public static function closeBook ( $args = array() )
	{
		return "</chapter></book>";
	}

}

?>