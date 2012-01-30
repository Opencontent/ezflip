<?php
//
// Definition of ezjscServerFunctionsJs class
//
// Created on: <16-Jun-2008 00:00:00 ar>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ JSCore extension for eZ Publish
// SOFTWARE RELEASE: 1.0-0
// COPYRIGHT NOTICE: Copyright (C) 2009 eZ Systems AS
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
 * Some ezjscServerFunctions
 */

class ezjscore_Search extends ezjscServerFunctions
{
	
    public static function searchContent( $args )
    {
		$http = eZHTTPTool::instance();
		$parent_node_id = $args[0];
		$class = $args[1];
		$available_classes = array('image','article','comment');

		if ((int) $parent_node_id>0 && in_array($class, $available_classes)) {
			$contentTree = eZContentObjectTreeNode::fetch( $parent_node_id );
			$classFilters = array(
				'ClassFilterType' => 'include',
				'ClassFilterArray' => array($class)
			);
			
			$childrenCountParams = array(
				'MainNodeOnly' => true,
				'Depth' => 1,
				'DepthOperator' => 'eq'
			);
			
			$childrenParams = array(
				'MainNodeOnly' => true,
				'Depth' => 1,
				'DepthOperator' => 'eq',			
				'Limit' => 200,
				'SortBy' => array( 'published', false )
			);
			
			if ( $class ) {
				$childrenCountParams = array_merge( $childrenCountParams, $classFilters );
				$childrenParams = array_merge( $childrenParams, $classFilters );
			}
			
			$children = eZContentObjectTreeNode::subTreeByNodeID(  $childrenParams, $parent_node_id );
			$result['results_count'] = eZContentObjectTreeNode::subTreeCountByNodeID(  $childrenCountParams, $parent_node_id );
			
			foreach ( $children as $i => $node ) {
				$data_map = $node->dataMap();
				$image  = $data_map['image']->content()->attribute('small');
				$result[$i+1] = $image['url'];
			}
			return $result;
		
		} else {
			
			return null;
			
		}	
		
    }
}
?>
