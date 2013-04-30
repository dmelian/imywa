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

/* STANDART ACTIONS
 * close - close current form.
 * 
 * pdf - print on pdf the current form.
 * csv - convert to comma-separated-values the current form.
 * 
 * [record]
 * last, first - Go to last or first and load the record
 * next, previous - Go to the next or previous record
 * 
 * [config]
 * theme - Select a new theme
 * language - Select another language.
*/
class bas_frmx_toolbar{
	public $actions;
	
	public function __construct($actions=''){
		global $_LOG;
		
		// Default actions
		$this->actions=array();
		$this->actions['first']['type']= 'ajax';
		$this->actions['first']['icon']= 'ia_icon ia_icon_primero';//ui-icon-arrowthickstop-1-w;
		$this->actions['first']['enabled']= false;
		$this->actions['first']['visible']= true;
		
		$this->actions['previous']['type']= 'ajax';
		$this->actions['previous']['icon']= 'ia_icon ia_icon_antes';
		$this->actions['previous']['enabled']= false;
		$this->actions['previous']['visible']= true;
		
		$this->actions['next']['type']= 'ajax';
		$this->actions['next']['icon']= ' ia_icon ia_icon_next';
		$this->actions['next']['enabled']= false;
		$this->actions['next']['visible']= true;
		
		$this->actions['last']['type']= 'ajax';
		$this->actions['last']['icon']= 'ia_icon ia_icon_ultimo';
		$this->actions['last']['enabled']= false;
		$this->actions['last']['visible']= true;
		
		$this->actions['pdf']['type']= 'submit';
		$this->actions['pdf']['icon']= 'ia_icon ia_icon_acrobat';
		$this->actions['pdf']['enabled']= false;
		$this->actions['pdf']['visible']= true;
	
	
		$this->actions['csv']['type']= 'submit';
		$this->actions['csv']['icon']= 'ia_icon ia_icon_excel';
		$this->actions['csv']['enabled']= false;
		$this->actions['csv']['visible']= true;
		
		$this->actions['filtro']['type']= 'ajax';
		$this->actions['filtro']['icon']= 'ia_icon ia_icon_filtro';
		$this->actions['filtro']['enabled']= false;
		$this->actions['filtro']['visible']= true;
		
		$this->actions['config']['type']= 'menu';
		$this->actions['config']['icon']= 'ia_icon ia_icon_vuser';
		$this->actions['config']['enabled']= false;
		$this->actions['config']['visible']= true;
		
		$menu = new bas_frmx_menubox("toolbar_config");
		$menu->addElement("primero");
		$menu->addElement("segundo");
		$menu->addElement("tercero");
		
		$aux = new bas_frmx_menubox("sub");
		$aux->addElement("primero");
		$aux->addElement("segundo");
		$aux->addElement("tercero");
		
		$menu->addElement("cuarto","",$aux);
		$this->actions['config']['menu']= $menu;

		$this->actions['close']['type']= 'ajax';
		$this->actions['close']['icon']= 'ia_icon ia_icon_salir';
		$this->actions['close']['enabled']= true;
		$this->actions['close']['visible']= true;
		
		
		// Enabled Actions
		if (! is_array($actions)) $actions= explode(',',$actions);
		foreach($actions as $action) {
			if (isset ($this->actions[$action])) {
				$this->actions[$action]['enabled']= true;
			} else {
				$_LOG.log("Action $action not defined.");
			}
		}
		
		// Config
		
	}
	
	public function stamp(){
		global $_SESSION;
		$toolbar= '<div id="toolbar">';
		$menus='';
		foreach($this->actions as $id => $action){
			if( ($action['visible']) && $action['enabled']){
				switch($action['type']){
				case 'submit': 
// 					$toolbar.="<button type=\"submit\" id=\"toolbar_$id\" name=\"action\" value=\"$id\"></button>";
					$toolbar.="<form style=\"display: inline-block;\" name=\"form_$id\" method=\"post\" enctype=\"multipart/form-data\">";
// 						sessionno
						$toolbar.= "<input type=\"hidden\" name=\"sessionId\" value=\"".$_SESSION->sessionId."\">";
						$toolbar.="<button id=\"toolbar_$id\" value=\"$id\" onclick=\"javascript:submitaction('".$id."');\"  title='genera un documento $id'></button>"; 
					$toolbar.="</form>";
					break;
				case 'ajax':
					switch($this->actions){
						case 'filtro':
							$toolbar.="<button id=\"toolbar_$id\" value=\"$id\" onclick=\"javascript:ajaxaction('".$id."');\" title='$id: permite iniciar una busqueda por parámetros'></button>";
							break;
						case 'close':
							$toolbar.="<button id=\"toolbar_$id\" value=\"$id\" onclick=\"javascript:ajaxaction('".$id."');\" title='$id: permite iniciar una busqueda por parámetros'></button>";
							break;}
						$toolbar.="<button id=\"toolbar_$id\" value=\"$id\" onclick=\"javascript:ajaxaction('".$id."');\" title=$id></button>";
					break;

				case 'menu':
					$toolbar.="<button id=\"toolbar_$id\" value=\"$id\" class=\"ia_menutool_button ia_menu_item\" title='Acción de $id'></button>";
					break;
					
				default:
					$toolbar.="<button id=\"toolbar_$id\"></button>";
//					$menus.= $action['menu']->stamp();
				}	
			}
		}
		return "$toolbar</div>$menus";
	}
	
// 	public function OnPaintMenu(){
// 	      return "";
// 	}
	
	public function OnPaintMenu(){
		$ret="";
		foreach ($this->actions as $id => $action){
			if($action['visible']){
// 				if (isset($this->actions[$action]['menu'])){
				if ($action['type'] == 'menu'){
					$ret .= $action['menu']->contentMenu();
				}
			}
	      }
	      return $ret;
	}
	
}


?>
