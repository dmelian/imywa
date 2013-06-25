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
class bas_frmx_buttonbar {
	public $id; 
	public $jsClass= "bas_frmx_buttonbar";
	public $actions=array();
	
	public function __construct($id='main'){
	    $this->id = $id;
	}
	
	private function addElement($id,$frame=NULL,$menu=NULL,$caption="", $type="ajax",$action=""){
		if ($caption == ""){
		    $caption = $id;
		}
		if ($action == ""){
		    $action = $id;
		}
		if (!isset($this->actions[$id])){
		    $this->actions[$id]["caption"]=$caption;
// 		    $this->actions[$id]["action"]=$action;
		    $this->actions[$id]["frame"]=$frame;
		    $this->actions[$id]["menu"]=$menu;
		    $this->actions[$id]["type"]=$type;
		}
		else{  // el identificador ya se utiliza.
			global $_LOG;
				$_LOG->log(get_class()."::addElement. Identificador $id");
		}
	}
	
	public function addAction($id,$caption="", $action=""){
		$this->addElement($id,null,null,$caption);
	}
	
	public function addframeAction($id,$frame,$caption="", $action=""){
		$this->addElement($id,$frame,null,$caption);
	}
	
	public function addSubmitAction($id,$caption="", $action=""){
		$this->addElement($id,null,null,$caption,"submit");
	}

	public function addEcliveAction($id,$caption="", $action=""){
			$this->addElement($id,null,null,$caption,"submiteclive");		
		}
	
	public function addMenu($id, $menu,  $caption="",$frame=NULL){
		$this->addElement($id,$frame,$menu,$caption);
		
	}
	
	
	public function OnPaint(){
	      echo "<div class='ia_list_actions'>";
		  $this->OnPaintContent();
	      echo "</div>";
	}
	
	
	private function OnPaintContent(){
		global $_SESSION;
		foreach ($this->actions as $action => $value){
			if ($this->actions[$action]['menu']!= null){
				echo " <button id='$action' class=\"ia_menubar_button ia_submenubar_button ia_menu_item\" >";
				echo $this->actions[$action]["caption"]."</button>";				
			
		// 					$this->OnPaintMenu($this->actions[$action]['menu'],$action); ### Desde ahora se pintara externamente
			}
			else{
				
				switch($this->actions[$action]['type']){

					case 'ajax':
						echo " <button id='$action' class=\"ia_menubar_button\" name=\"".$action."\"";
		// 					if (isset($this->actions[$action]["frame"])) echo "onclick=\"javascript:frameAction('".$this->actions[$action]["action"]."','".$this->actions[$action]["frame"]."');\" >";		
						if (isset($this->actions[$action]["frame"])) echo "onclick=\"javascript:frameAction('".$action."','".$this->actions[$action]["frame"]."');\" >";
						else echo "onclick=\"javascript:ajaxaction('".$action."');\" >";
						echo $this->actions[$action]["caption"]."</button>";			

					break;
					case 'submiteclive':
						echo "<form style=\"float: left;\" action='../ecl/validaec.php' name=\"form_down$action\" method=\"post\" enctype=\"multipart/form-data\">";
						echo "<input type=\"hidden\" name=\"us\" value=\"".$_SESSION->user."\">";
						echo "<input type=\"hidden\" name=\"cups\" value=\"\">";
						echo "<a id=\"cluz_$action\" class=\"ia_eclive_button ia_menubar_button\" name=\"".$action."\" value=\"$action\" formtarget='_blank' onclick=\"currentForm.sendEclive();\">"
								.$this->actions[$action]["caption"]."</a>"; 
						echo "</form>";

					break;
					default:
						echo "<form style=\"float: left;\" name=\"form_down$action\" method=\"post\" enctype=\"multipart/form-data\">";
						echo "<input type=\"hidden\" name=\"sessionId\" value=\"".$_SESSION->sessionId."\">";
						echo "<a id=\"menulbar_$action\" class=\"ia_menubar_button\" name=\"".$action."\" value=\"$action\" onclick=\"javascript:ajaxaction('".$action."');\">"
								.$this->actions[$action]["caption"]."</a>"; 
						echo "</form>";
				}

					//if ($this->actions[$action]['type']!= 'ajax'){
					
					
					
// 					echo "<button id=\"menulbar_$action\" class=\"ia_menubar_button\" name=\"".$action."\" value=\"$action\" onclick=\"javascript:submitaction('".$action."');\">"
// 							.$this->actions[$action]["caption"]."</button>"; 
// 					echo "</form>";
				//}
				//else{
						
				//}
			}

		}

	}
	
	public function OnPaintMenu(){
		$ret="";
		foreach ($this->actions as $action => $value){
// 				if (isset($this->actions[$action]['menu'])){
				if ($this->actions[$action]['menu']!= null){
					$ret .= $this->actions[$action]['menu']->contentMenu();
				}
	      }
	      return $ret;
	}
	

	
// ### Código previo a la creacion de la clase frmx_menu

// 	public function contentMenu(){
// 		$ret="";
// 		ob_start();
// 		foreach ($this->actions as $action => $value){
// 			if (isset($this->actions[$action]['menu'])){
// 				echo " <a id='$action' class=\"ia_menubar_button\" >";
// 				echo $this->actions[$action]["caption"]."</a>";		
// 				$this->OnPaintMenu($this->actions[$action]['menu'],$action);
// 			}
// 		}
// 		$ret = ob_get_contents();
// 		ob_end_clean();
// 		return $ret;
// 	}
// 	
// 	private function OnPaintMenu($menu,$id,$level=0){
// 		echo "<ul";
// 		if ($level == 0) echo " id=\"sbmenu_$id\" class = \"ia_menubar\"";
// 		echo ">";
// 		foreach ($menu as $action => $value){
// 			
// // 				echo " <li> <a id='$action' name='".$menu[$action]["action"]."'
// // 					onclick=\"javascript:submitaction('".$menu[$action]["action"]."'); \""."'>";
// 
// 				echo " <li> <a id='$action' name='".$menu[$action]["action"]."'
// 					onclick=\"javascript:currentForm.sendAction('".$menu[$action]["action"]."');\""."'>";
// 
// 				echo $menu[$action]["caption"]."</a>";
// 				
// 				if (isset($menu[$action]['menu'])){
// 					$this->OnPaintMenu($menu[$action]['menu'],$id,$level+1);
// 				}
// 				echo "</li>";
// 	      }
// 	    echo "</ul>";
// 	}

// 	public function addMenu($path,$id, $captions="", $actions=""){
// 		$menu  = split(' > ', $path);
// 		
// 		$nelemt = count ($menu);
// 		$dir = &$this->actions[$menu[0]];
// 		
// 		for($ind = 1;$ind < $nelemt;$ind++){
// 			if (isset($dir['menu'][$menu[$ind]])){ // Recorremos el directorio hasta localizar el ultimo nodo
// 				$dir = &$dir['menu'][$menu[$ind]];				
// 			}
// 			else{ // Se ha producido un error
// 				global $_LOG;
// 				$_LOG->log("Path errónea, no existe el camino indicado {$dir['caption']}/menu/{$menu[$ind]}. ".get_class()."::addMenu");
// 				return;
// 			}		
// 		}
// 		
// 		$nelemt = count ($id);
// 		
// 		if ($captions == "") $captions = $id;	
// 		if ($actions == "") $actions = $id;
// 		
// 		for($ind = 0;$ind < $nelemt;$ind++){
// 			$this->addElement($dir['menu'],$id[$ind],$captions[$ind], $actions[$ind]);
// 		}
// 		
// 	}
	
	
}
?>
