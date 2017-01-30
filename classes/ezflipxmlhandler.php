<?php

class eZFlipXmlHandler
{

	public function eZFlipXmlHandler(){}
	
	
	public static function writeBookOpen ( $options = array() )
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
			$args['pageheight'] = round ( $args['pagewidth'] * $args['ratio'] );
		
		$xml = "<?xml version='1.0' encoding='utf-8'?>
		<!DOCTYPE book SYSTEM 'http://megazine.mightypirates.de/megazine.dtd'>
		<book lang='" . $args['lang'] . "' pagewidth='" . $args['pagewidth'] . "' pageheight='" . $args['pageheight'] . "' zoominit='" . $args['zoominit'] . "' zoomrotate='" . $args['zoomrotate'] . "' zoomcontrolalpha='" . $args['zoomcontrolalpha'] . "' navigation='" . $args['navigation'] . "'>
			<chapter>";
			
		return $xml;
	
	}

	public static function writePage ( $index, $thumb, $full, $file, $object_id = 0, $options = array() )
	{
		
		if ( $object_id == 0 )
		{
			eZDebug::writeError( 'No object_id found', __METHOD__ );
			return false;
		}
		
		$defaultArgs = array(
			'position' => 'bottom',
			'srcpath' => 'application_flip/',
			'gallery' => 'Opencontent'
		);
		
		$args = array_merge( $defaultArgs, $options );
		
		/*
        $xml = "<page>
			<img position='" . $args['position'] . "' src='" . $args['srcpath'] . $object_id . "/page" . sprintf("%04d", $index) .  "_" . $thumb .".jpg' hires='" . $args['srcpath'] . $object_id . "/page" . sprintf("%04d", $index) .  "_" . $full .".jpg' gallery='" . $args['gallery'] . "'/>
			<img url='" . $args['srcpath'] . $object_id . "/" . $file . "' src='" . $args['srcpath'] . "pdf.jpg' left='40' top='5' title='Scarica questa pagina in formato PDF'/>
		</page>";
        */

		$xml = "<page>
			<img position='" . $args['position'] . "' src='" . $args['srcpath'] . $object_id . "/page" . sprintf("%04d", $index) .  "_" . $thumb .".jpg?=_" . time() . "' hires='" . $args['srcpath'] . $object_id . "/page" . sprintf("%04d", $index) .  "_" . $full .".jpg?=_" . time() . "' gallery='" . $args['gallery'] . "'/>
		</page>";
		
		return $xml;
	
	}

	public static function writeBookClose ( $args = array() )
	{
		return "</chapter></book>";
	}
	
	public static function createXml ( $id= '', $xml = '', $object_id = 0, $flipFolder = false )
	{

		if ( $object_id == 0 )
		{
			eZDebug::writeError( 'No object_id found', __METHOD__ );
			return false;
		}
			
		if ( !$flipFolder )
		{
			$ini = eZINI::instance( 'site.ini' );
			$var = $ini->variable( 'FileSettings','VarDir' );
			$flip_dir = $var . '/storage/original/application_flip/';
			$flipFolder = $flipDir.$object_id;
		}
		
		$fp = fopen( $flipFolder . "/magazine_" . $id . ".xml", 'w');
		fwrite($fp, $xml);
		fclose($fp);
		
		return true;
	}
	
}

?>