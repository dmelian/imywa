<?php
/*
	Copyright 2009-2012 Domingo Melian

	This file is part of imywa.

	imywa is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	imywa is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with imywa.  If not, see <http://www.gnu.org/licenses/>.
*/
/**
 * @package html
 */
class bas_htm_menu{

	protected $menus=array();
	private $id;
	
	public function __construct($id='main'){
		$this->id = $id;
	}
	
	public function add($caption, $id=''){
		$this->menus[] = array('caption'=>$caption, 'id'=>$id);
	}
	
	public function addmenu($menu, $caption){
		$this->menus[] = array('menu'=>$menu, 'caption'=>$caption);
	}
	
	
	public function printme($level=0){
		# los encabezados
		$menuclass = $level ? 'submenu' : 'menu';
		echo "<ul id=\"tab_$this->id\" class=\"$menuclass\">\n";
		foreach($this->menus as $menu){
			echo "<li>";
			if (isset($menu['id'])) echo "<a href=\"#\" onClick=\"submitaction('${menu['id']}');return false;\">";
			echo $menu['caption'];
			if (isset($menu['id'])) echo "</a>";
			if (isset($menu['menu'])) $menu['menu']->printme($level+1);
			echo "</li>\n";
		}
		echo "</ul>\n";
	}
	
	
}
?>
