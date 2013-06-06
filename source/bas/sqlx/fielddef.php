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
 * Esta es una clase de prueba para soportar conjuntos de datos formados por querys de varias tablas
 * en contraposición con bas_dat_tabledef que se definió para una sola tabla.
 * En principio como no estamos seguros de como saldrá un intento de fusión de las dos, y como la primera
 * funciona muy bien lo hemos pasado a una clase nueva
 * Esto salió por primera vez en vigilancia. Las visitas activas.
 */
class bas_sqlx_fielddef {
	
	public $id, $type;
	public $expression, $visible, $caption;
	public $align;
	public $editable, $name;
	public $pk,$aliasof;
	public $filter,$selected;
	
	public $description;
	public $reference;
	
	public $lookup,$lookupcaption;
	public $db;
	public $indexTab;
	
	//($id,$table,$aliasof,$pk,$caption)
	public function __construct($id,$table,$db="",$pk=false,$caption="",$aliasof=NULL,$editable=true,$visible=true,$selected=true){
	    $this->id = $id;
	    $this->table = $table;
	    $this->pk = $pk;
	    
	    $this->caption = $caption;
	    $this->aliasof = $aliasof;
	    
	    $this->editable = $editable;
	    $this->visible = $visible;	   
	    $this->selected = $selected;
	    $this->lookup = $this->lookupcaption = "";
	    $this->indexTab = $this->description = $this->reference = $this->expression= "";
	    
	    $this->name = $id;
	    $this->db = $db;
	    $this->type= "abstract";
	    $this->align = "left";
	}
	
	public function OnPaint($value, $labelwidth='30',$caption=true,$mode="edit"){ // Introduciría más funciones como por ejemplo una que "pinte" el caption
	    if ($this->visible){
		echo "<div style=\"position:absolute;width:100%; height:100%;\" >";
			$this->OnContent($value,$labelwidth,$caption,$mode);
		echo "</div>";
	    }
	}
	
	public function Show($value){ // Introduciría más funciones como por ejemplo una que "pinte" el caption
	    if ($this->visible){
		echo "<div style=\"position:relative;width:100%;height:100%;\" >";
			$this->OnContent($value,30,true,"edit");
		echo "</div>";
	    }
	}
	
	public function setAttr($attr,$value){
		if (isset($this->$attr))$this->$attr = $value;
		else{
			global $_LOG;
			$_LOG->log("Atributo desconocido $attr en ".get_class()."::SetAtt");
		}	
	}
	
	protected function OnPaintCaption($labelwidth){
		echo "<div class=\"ia_labelfield\" style=\"padding-top: 4pt;width:20%\">".$this->caption."</div>"; //$labelwidth
	}
	
// 	protected function OnPaintLookup(){
// 		echo "<button class=\"lookup\" name=\"{$this->lookup}\"style=\"display:inline-block;margin-top:4pt;\" ";
//  			echo "onclick=\"javascript:submitlookup(this,'{$this->lookup}');\">";
// 			echo "<span class=\"ui-icon ui-icon-tag\"></span>";
// 		echo "</button>";
// 	}
	
	protected function OnPaintLookup(){
		echo "<button class=\"lookup\" name=\"{$this->lookup}\"style=\"display:inline-block;margin-top:4pt;\" >";
			echo "<span class=\"ui-icon ui-icon-search\"></span>";
		echo "</button>";
	}	
	
	
	
	public function OnContent($value,$labelwidth,$caption,$mode){
	    // Cada tipo deberá implementar su función OnPaint para mostrar el contenido html
	}
	
	public function OnCheck($value){
	    // Cada tipo deberá implementar su función check para revisar que el contenido posee el formato exigido.
	}	
	
	public function OnPdf($value){
		
	}
	
	public function OnCsv($value){
		
	}
	
	public function OnFormat($value,$job="read"){
		return $value;
		
	}
}

?>
