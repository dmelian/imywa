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
class bas_sqlx_cronoQuery extends bas_sqlx_querydef {

	public function __construct($caption="", $id=""){
		global $CONFIG;
		parent::__construct($caption, $id);
	}
    
    public function setCrono($date, $value, $operation="sum"){
        $this->addrelatedExp("cronoHeaders",$date,"registros","imywa","between imywa.cronoHeaders.fromDate  and imywa.cronoHeaders.untilDate","inner");  // ### Cuidado con la tabla registros
        
        $this->addcol('name','name','cronoHeaders',true,"imywa");
        $this->setGroup('name');
        $this->setGroup('tarea');   //### DEbe estar fuera
        
        $this->addcol('cronoValue','Valor','registros',false,"","number");
        $this->getField('cronoValue')->setAttr('expression',"sum(registros.$value)");
    
        $this->addcondition("registros.registrado=1");  //### DEbe estar fuera
    }

  
	
}

?>
