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
class bas_htm_formmenu{

	protected $menu=array('menu'=>array());
	
	
	public function addmenu($id, $caption, $action=''){
		$key = explode('.',$id);
		$menu =& $this->menu;
		for ($i=0; $i<count($key); $i++){
			if (!isset($menu['menu'][$key[$i]])) $menu['menu'][$key[$i]] = array('id'=>$key[$i],'caption'=>$key[$i],'action'=>'');
			$menu =& $menu['menu'][$key[$i]];
		}
		$menu['caption'] = $caption;
		$menu['action'] = $action;
	}
	
	private function printmenu($id='', $menu, $s=''){
		if ($id) $id = " id=\"$id\"";
		
		echo "$s<ul$id>\n";	
		foreach ($menu as $dfmenu){
			if (isset($dfmenu['checked'])) $checked = " class=\"checked\"";
			else $checked = '';
			echo "$s\t<li$checked><a href=\"#\" onClick=\"submitaction('${dfmenu['action']}');return false;\">{$dfmenu['caption']}</a>\n";
			if (isset($dfmenu['menu'])) $this->printmenu('', $dfmenu['menu'], "$s\t");
			echo "$s\t</li>\n";
		}
		echo "$s</ul>\n";
		
	}
	
	public function printme(){
		
		$this->printmenu($this->menu['menu'], 'formmenu', 'mainMenu');
		
	}
	
	public function printdivmenubar(){
		foreach($this->menu as $menu){
			$linkto = isset($menu['menu']) ? " id=\"linkto{$menu['id']}\"": '';
			$onclick = ($linkto == '') && ($menu['action'] != '') ? " onClick=\"submitaction('${menu['action']}');return false;\"" : '';
			echo "\t<a href=\"#\"$linkto$onclick>{$menu['caption']}</a>\n";
		}
		
	}
	
	public function printdivmenus(){
		foreach($this->menu as $menu){
			if (isset($menu['menu'])) $this->printmenu($menu['id'], $menu['menu'], '');
		}
	}
		
	
}
?>
