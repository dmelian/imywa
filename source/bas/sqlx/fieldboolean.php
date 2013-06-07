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
class bas_sqlx_fieldboolean extends bas_sqlx_fieldtext {
	
	public function __construct($id,$table,$db="",$pk=false,$caption="",$aliasof=NULL,$editable=true,$visible=true,$selected=true){
		parent::__construct($id,$table,$db,$pk,$caption,$aliasof,$editable,$visible,$selected);
		$this->type = "boolean";	
		$this->align = "center";

	}
	
// 	public function OnContent($value,$labelwidth,$caption){
// 	    // Cada tipo deberá implementar su función OnPaint para mostrar el contenido html
// 	    if($caption) echo "<label style=\"width:$labelwidth%\">".$this->caption."</label>";
// 	    echo "<input type=\"checkbox\"";
// 	    if ($content) echo "checked=\"checked\"";
// 	    echo "name=\"".$this->name."\" value=\"".$this->value."\">";	    
// 	    echo "<br>";
// 	}
	
	protected function OnPaintInPut($value,$mode,$list=false){
		if ($list)	echo "<input type=\"checkbox\" style=\"width:100%\"";
		else echo "<input type=\"checkbox\"";
		
	    if ($value == "Sí") echo "checked=\"checked\"";
	    if (!$this->editable || $mode =="read"){
	      echo "disabled=\"disabled\"";	    
	    }
	    echo "name=\"".$this->name."\">";	    
	    echo "<br>";
	}
	
	public function OnPaintList($value,$mode){
// 		echo "<div style=\"width:100%;position:absolute;\">";
			$this->OnPaintInPut($this->OnFormat($value),$mode,true);
// 	    echo "</div>";
	}
	
	public function check($content){
	    // Cada tipo deberá implementar su función check para revisar que el contenido posee el formato exigido.
	}
	
	public function OnFormat($value){
// 	    if ($value)    return "Sí"; // Debemos de sustituirlo por su traducción correspondiente (por idiomas).
// 	    else return "No";
		if ($value === "") return "";
		if (($value==true)||($value==1)||($value==='true'))	return "Sí";
		else return "No";
// 		return $value;
	}
}
?>
