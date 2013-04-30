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
class bas_sqlx_fieldimage extends bas_sqlx_fielddef {
	private $bas_src;
	private $source;
	protected $mode;
	public function __construct($id,$table,$db="",$pk=false,$caption="",$aliasof=NULL,$editable=true,$visible=true,$selected=true){
		parent::__construct($id,$table,$db,$pk,$caption,$aliasof,$editable,$visible,$selected);
		global $_SESSION;
		$this->type = "image";	
		$this->bas_src = "upload/".$_SESSION->apps[$_SESSION->currentApp]->source."/docs/";//	$this->bas_src = "/upload/"+$_SESSION->currentApp+"/";
 		$this->source = $this->bas_src;			//$this->source = "upload/contaluz/docs/";//$this->bas_src;
 		$this->mode = "read";
	}
	
	public function OnContent($value,$labelwidth,$caption,$mode){

	    // Cada tipo deberá implementar su función OnPaint para mostrar el contenido html
// 	    if($caption) $this->OnPaintCaption($labelwidth);
		if (($this->mode != "read")&&($mode != "read")){
		    echo "<img id=\"{$this->id}_img\" class =\"thumbnail\" src=\"{$this->source}u_$value.jpg\" alt=\"{$this->caption}\" align=\"center\" style=\"height: 92%; width: 70%;display: inline-block;\" >";
			echo "<input id='{$this->id}' name='{$this->id}' type='file'>";
		}
		else
		    echo "<img id=\"{$this->id}_img\" class =\"thumbnail\" src=\"{$this->source}u_$value.jpg\" alt=\"{$this->caption}\" align=\"center\" style=\"height: 95%; width: 95%;\" >";

	    // echo "<img class =\"thumbnail\"src=\"image/icon_dlginfo.png\" alt=\"{$this->caption}\" align=\"center\" \>";
	    
	}

	public function check($content){
	    // Cada tipo deberá implementar su función check para revisar que el contenido posee el formato exigido.
	}
	
	public function OnFormat($value){
		return $value;
	}

}
?>
