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
class bas_sqlx_fieldmoney extends bas_sqlx_fieldtext {
	
	public function __construct($id,$table,$db="",$pk=false,$caption="",$aliasof=NULL,$editable=true,$visible=true,$selected=true){
		parent::__construct($id,$table,$db,$pk,$caption,$aliasof,$editable,$visible,$selected);
		$this->type = "money";	
		$this->align = "right";
	}
	
	public function OnFormat($value,$job="read"){
	//    setlocale(LC_MONETARY, 'es_ES');
	//    return money_format('%.2n €', $value);
		if ($value === "") return "";
		else if(!is_numeric($value))$value=floatval($value);/*si no es vacio y es una string hacemos una conversión a float SI SE PASA UN OBJETO DA ERROR*/
		if ($job == "read")	 return number_format($value, 2, ',', '.')."€";
		else return number_format($value, 2, ',', '.');
	}

	public function check($value){
	    // Cada tipo deberá implementar su función check para revisar que el contenido posee el formato exigido.
	}

}
?>
