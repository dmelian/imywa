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
class bas_frmx_menu extends bas_frmx_frame{
	public $jsClass= "bas_frmx_menuframe";
	protected $menus=array();
	
	public function __construct($id='main',$title=''){
		parent::__construct($id,$title);
	}
	
	public function add($caption, $id=''){
		if ($id == '') $id = $caption;
		$this->menus[] = array('caption'=>$caption, 'id'=>$id);
	}
	
	public function addmenu($menu, $caption){
		$this->menus[] = array('menu'=>$menu, 'caption'=>$caption);
	}
	
	
/*	public function OnPaintContent($level=0){
		# los encabezados
		$menuclass = $level ? 'submenu' : 'menu';
		echo "<ul id=\"menu_$this->id\" class=\"ia_menuframe\">\n";
		foreach($this->menus as $menu){
			echo "<li>";
			if (isset($menu['id'])) echo "<a href=\"#\" onClick=\"submitaction('${menu['id']}');return false;\">";
			echo $menu['caption'];
			if (isset($menu['id'])) echo "</a>";
			if (isset($menu['menu'])) $menu['menu']->OnPaintContent($level+1);
			echo "</li>\n";
		}
		echo "</ul>\n";
	}*/
	
	
	
		public function OnPaintContent($form="",$level=0){ // El form es obligatorio ya que en la clase form se envia un objeto de este tipo.
		# los encabezados
		$menuclass = $level ? 'submenu' : 'menu';
		if ($level == 0)echo "<ul id=\"menu_{$this->title}\" class=\"ia_menuframe\">\n";
		else echo "<ul>";
		foreach($this->menus as $menu){
			echo "<li>";
			if (isset($menu['id'])){
//				echo "<a href=\"#\" onClick=\"submitaction('${menu['id']}');return false;\">";
				echo "<a onClick=\"currentForm.sendAction('${menu['id']}');\">";
				echo $menu['caption'];
				echo "</a>";
			}
			if (isset($menu['menu'])) $menu['menu']->OnPaintContent($form,$level+1);
			echo "</li>\n";
		}
		echo "</ul>\n";
	}
	
	
	
	
}
?>
