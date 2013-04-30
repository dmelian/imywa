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
class bas_sqlx_fieldupload extends bas_sqlx_fielddef {
	private $bas_src;
	protected $mode;
	public $source;
	protected $prefix;
	
	public function __construct($id,$table,$db="",$pk=false,$caption="",$aliasof=NULL,$editable=true,$visible=true,$selected=true){
		parent::__construct($id,$table,$db,$pk,$caption,$aliasof,$editable,$visible,$selected);
		global $_SESSION;
		$this->type = "upload";	
		$this->bas_src = "upload/".$_SESSION->apps[$_SESSION->currentApp]->source."/docs/";
 		$this->source = $this->bas_src;
 		$this->mode ="normal";
 		$this->prefix="u_";
	}
	
	public function OnContent($value,$labelwidth,$caption,$mode){
	
		global $_LOG;
		$_LOG->log("#### fieldUPLOAD:: {$this->source}");

	    // Cada tipo deberá implementar su función OnPaint para mostrar el contenido html
	    
	    if($caption) $this->OnPaintCaption($labelwidth);
	    
	    switch ($this->mode){
			case "normal":
				echo "<a target=\"_blank\" href='{$this->source}$value'>".$value."</a>";// ###! tenemos que poner la accion con el onclick.
			break;
			case "visible":
				echo "<iframe src='{$this->source}$value' style='width:95%; height:95%;'></iframe>"; // mode = navigate;
			break;
			case "edit":
				echo "<input id='{$this->id}' name='{$this->name}' type='file'>";  
			break;
	    }
	}

	public function check($content){
	    // Cada tipo deberá implementar su función check para revisar que el contenido posee el formato exigido.
	}
	
	public function OnFormat($value){
// 		return $value;
		return	"<a target=\"_blank\" href=\"{$this->source}{$this->prefix}$value\">".$value."</a>";
	}

}
?>
