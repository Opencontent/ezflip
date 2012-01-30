<?php
//
// Definition of ezflipServerFunctions class
//
// Created on: <31-Jul-2009 00:00:00 ar>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Flip
// SOFTWARE RELEASE: 1.1-0
// COPYRIGHT NOTICE: Copyright (C) 2009-2010 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

/*
 * ezjscServerFunctions for ezflip
 */

class ezflipServerFunctions extends ezjscServerFunctions
{

    public static function convert( $args )
    {
        return time();
    }
	
	public static function checkScript()
    {        
		return 1;
    }
	
	public static function preparePdf()
	{		
		$files = ezFlip::preparePdf( $_POST['vars'] );
		if ( isset( $files['errors'] ) )
			return array( 'error' => $files['errors'] );
		elseif ( count( $files ) < 1 )
			return array( 'error' => 'file protetto da password o corrotto' );
		else	
			return array(
				'pages' => count( $files ),
				'files' => $files
			);
	}
	
	public static function convertImage()
	{	
		$ret = ezFlip::checkArgs( $_POST['vars'] );
		$result = ezFlip::createImage( $_POST['index'], $_POST['file'], $ret );
		include_once( 'kernel/common/template.php' );
		$tpl = templateInit();
		$tpl->setVariable( 'result', $result->attribute( 'main_node_id' ) );
		$templateOutput = $tpl->fetch( 'design:ezflip/image_preview.tpl' );
		return array( 'index' => $_POST['index'], 'thumb' => $templateOutput );
	}

	public static function createBook()
	{		
		$ret = ezFlip::checkArgs( $_POST['vars'] );
		$flip_folder = ezFlip::flipFolderPath ( $ret['object_id'] );
		$files = ezFlip::readFlipFolder( $flip_folder, true );
		ezFlip::createBook( $files, $ret );
		return true;
	}	

	
}

?>
