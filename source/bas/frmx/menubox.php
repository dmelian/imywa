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
class bas_frmx_menubox{
// 	public $jsClass= "bas_frmx_menuframe";
	protected $id;
	protected $caption;
// 	protected $menu;
	protected $actions=array();
	
	public function __construct($id='main'){//,$caption=''){
		$this->id = $id;
// 		$this->caption = $caption;
	}
	
	public function add($caption, $id=''){
		if ($id == '') $id = $caption;
		$this->menus[] = array('caption'=>$caption, 'id'=>$id);
	}
	
	public function addElement($id,$caption="",$menu="", $action=""){
		if ($caption == ""){
		    $caption = $id;
		}
		
		if (!isset( $this->actions[$id])){
		    $this->actions[$id]["caption"]=$caption;
// 		    $vec[$id]["action"]=$action;
			if ( $menu != "") $this->actions[$id]["menu"]=$menu;
		}
		else{  // el identificador ya se utiliza.
			global $_LOG;
				$_LOG->log(get_class()."::addElement. Identificador $id ya utilizado.");
		}
	}
	
	
	public function contentMenu(){
		$ret="";
// 		ob_start();
			$this->OnPaintMenu($this->id,0);
		$ret = ob_get_contents();
		ob_clean();
// 		ob_end_clean();
		return $ret;
	}
	
	private function OnPaintMenu($id,$level=0){
		echo "<ul";
		if ($level == 0) echo " id=\"submenu_$id\" class = \"ia_menubar\"";
		echo ">\n";
		foreach ($this->actions as $action => $value){
			echo "<li>";
			if (isset($this->actions[$action]['menu'])){
// 				echo " <a id='$action' class=\"ia_menu_button\" >";
				echo " <a class=\"ia_submenu_item\" >";
				echo $this->actions[$action]["caption"]."</a>";				
				$this->actions[$action]['menu']->OnPaintMenu("",$level+1);
			}
			else{
				echo "<a onClick=\"currentForm.sendAction('".$action."');\">";
				echo $this->actions[$action]["caption"];
				echo "</a>";				
			}
// 				echo " <li> <a id='$action' name='".$this->actions[$action]["action"]."'
// 					onclick=\"javascript:currentForm.sendAction('".$this->actions[$action]["action"]."');\""."'>";

			echo "</li>\n";
	      }
	    echo "</ul>\n";
	}	
}
?>
