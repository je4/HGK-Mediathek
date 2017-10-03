<?php
/**
 * This file is part of MediathekMiner.
 * MediathekMiner is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * Foobar is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details
 *
 * You should have received a copy of the GNU General Public License along with Foobar. If not, see http://www.gnu.org/licenses/.
 *
 *
 * @package     Mediathek
 * @subpackage  SOLRResult
 * @author      Juergen Enge (juergen@info-age.net)
 * @copyright   (C) 2016 Academy of Art and Design FHNW
 * @license     http://www.gnu.org/licenses/gpl-3.0
 * @link        http://mediathek.fhnw.ch
 * 
 */

/**
 * @namespace
 */

namespace Mediathek;

class CategoryTree {
	protected $level, $parent, $name, $count, $selected;
	protected $children = array();
	protected $label;
	protected $open = false;
	
	static function buildTree( $categories, $selected ) {
		//var_dump( $selected );
		while( ($key = array_search( 0, $categories )) !== false )
			unset( $categories[$key] );
			
		foreach( $selected as $sel ) {
			if( !array_key_exists( $sel, $categories )) {
				$kl = explode( '!!', $sel );
				array_shift( $kl );
				while( count( $kl ) > 0 ) {
					$k = (count( $kl )-1).'!!'.implode( '!!', $kl );
					if( !array_key_exists( $k, $categories )) {
						$categories[$k] = 0;
					}
					array_pop( $kl );
				}
			}
		}
		$cats = array_keys( $categories );
		sort( $cats );
		$root = new CategoryTree( 0, null, 'root', 'root', 0, false );
		foreach( $cats as $cat ) {
			$root->addChild( explode( '!!', $cat ), $categories[$cat], in_array( $cat, $selected ));
		}
		return $root;
	}
	
    public function __construct( $level, $parent, $name, $label, $count, $selected ) {
		$this->level = $level;
		$this->parent = $parent;
		$this->name = $name;
		$this->count = $count;
		$this->selected = $selected;
		$this->label = $label;
		if( $selected ) $this->open();
		//echo "__construct( ".$level.', '.$label.', '.$name.")\n";
    }

	public function __toString() {
		$str = sprintf( "%{$this->level}s%s", ' ', $this->label );
		foreach( $this->children as $child ) {
			$str .= "\n".$child;
		}
		return $str;
	}
	
	public function open() {
		$this->open = true;
		if( $this->parent != null )
			$this->parent->open();
	}
	
	public function treeJS() {
		$str = '';
		if( $this->parent != null ) {
			$str .= '<li id="'.$this->name.'" data-jstree=\'{"opened":'.($this->open ? 'true':'false').', "selected":'.($this->selected ? 'true':'false').'}\'>'.
			$str .= '<a href="#" class="'.($this->open ? 'jstree-open':'').'">'.htmlspecialchars( $this->label )." (".number_format( $this->count, 0, '.', "'" ).")</a>\n";
		}
		if( count( $this->children )) {
			$str .= "   <ul>\n";
			foreach( $this->children as $child ) {
				$str .= $child->treeJS();
			}
			$str .= "   </ul>\n";
		}
		if( $this->parent != null )
			$str .= '</li>'."\n";
		return $str;
	}
	
	public function addChild( $cat, $count, $selected ) {
		//echo "{$this->name}[{$this->level}]--addChild( ".implode( '-', $cat ).", {$count}, {$selected} );\n";
		$level = intval( $cat[0] );
		if( $level < $this->level ) {
			return false;
		}
		elseif( $level == $this->level ) {
			$name = implode( '!!', $cat );
			$part = $cat[$this->level+1];
			$child = new CategoryTree( $level+1, $this, $name, $part, $count, $selected );
			$this->children[$part] = $child;
		}
		else {
			$part = $cat[$this->level+1];
			if( array_key_exists( $part, $this->children )) {
				$this->children[$part]->addChild( $cat, $count, $selected );
			}
		}
	}
 
}