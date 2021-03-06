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
class bas_sqlx_fieldtext extends bas_sqlx_fielddef {
	
	public function __construct($id,$table,$db="",$pk=false,$caption="",$aliasof=NULL,$editable=true,$visible=true,$selected=true){
		parent::__construct($id,$table,$db,$pk,$caption,$aliasof,$editable,$visible,$selected);
		$this->type = "text";	
		$this->align = "center";
	}
	
	public function OnContent($value,$labelwidth,$caption,$mode){
// 	    Pintamos el caption del componente
	    if($caption) $this->OnPaintCaption($labelwidth);
	    
// 	    Creamos el inputbox con su contenido
	    $this->OnPaintInPut($this->OnFormat($value,"input"),$mode);
	    
// 	    Si posee lookup creamos el boton y su acción javascript
	    if ($this->lookup!=""){
			$this->OnPaintLookup();			
	    }
	}
	
	protected function OnPaintInPut($value,$mode,$list=false){
		if ($list)	echo " <input class=\"ia_inputfield\" type=\"text\" style=\"background-color: white;width:100%;text-align:{$this->align}\"name=\"".$this->name."\" value=\"".$value."\"";
		else 	echo " <input class=\"ia_inputfield\" type=\"text\" style=\"background-color: white;text-align:{$this->align}\"name=\"".$this->name."\" value=\"".$value."\"";
		if ($this->indexTab) echo "tabindex=\"{$this->indexTab}\"";
	    if (!$this->editable || $mode =="read"){
	      echo "disabled=\"disabled\"";	    
	    }
	    echo  ">";
	}
	
	public function OnPaintList($value,$mode){
// 		echo "<div style=\"width:100%;position:absolute;\">";
			$this->OnPaintInPut($this->OnFormat($value,$mode),$mode,true);
// 	    echo "</div>";
 	}
	public function check($content){
	    // Cada tipo deberá implementar su función check para revisar que el contenido posee el formato exigido.
	}
	
	public function OnFormat($value,$job="read"){
		
		return $value;
	}

}
?>
