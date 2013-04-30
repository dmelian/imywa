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
 * @package actions
 *
 */
class bas_act_card{
	private $objectid; // Valorar el usar $tabledef->tables[0]['table] en lugar de dar de alta un identificador;

	public function __construct(&$actions, &$toolbar, $id=''){
/*		$actions["${id}nuevo"] = array('id'=>'nuevo', 'group'=>"${id}card");
		$actions["${id}editar"] = array('id'=>'editar', 'group'=>"${id}card");
		$actions["${id}borrar"] = array('id'=>'borrar', 'group'=>"${id}card");
		//$actions["${id}buscar"] = array('id'=>'buscar', 'group'=>"${id}card");
		$actions["${id}findrecord"] = array('id'=>'seekrecord', 'group'=>"${id}card");
		$actions["${id}primero"] = array('id'=>'primero', 'group'=>"${id}card");
		$actions["${id}anterior"] = array('id'=>'anterior', 'group'=>"${id}card");
		$actions["${id}siguiente"] = array('id'=>'siguiente', 'group'=>"${id}card");
		$actions["${id}ultimo"] = array('id'=>'ultimo', 'group'=>"${id}card");
*/		
		$actions["${id}lookup"] = array('id'=>'lookup', 'group'=>"${id}card");
		$actions["${id}seek"] = array('id'=>'seek', 'group'=>"${id}card");
		$actions["${id}setvalues"] = array('id'=>'setvalues', 'group'=>"${id}card");
		
/*		$toolbar[] = array('id'=>"${id}nuevo", 'image'=>'nuevo', 'description'=>'Crea una nueva ficha.');
		$toolbar[] = array('id'=>"${id}editar", 'image'=>'editar', 'description'=>'Modifica la ficha.');
		$toolbar[] = array('id'=>"${id}borrar", 'image'=>'borrar', 'description'=>'Borra la ficha.');
		//$toolbar[] = array('id'=>"${id}buscar", 'image'=>'buscar', 'description'=>'Busca una ficha por valores de clave.');
		$toolbar[] = array('id'=>"${id}primero", 'image'=>'primero', 'description'=>'Ir al primero.');
		$toolbar[] = array('id'=>"${id}anterior", 'image'=>'anterior', 'description'=>'Ir al anterior.');
		$toolbar[] = array('id'=>"${id}siguiente", 'image'=>'siguiente', 'description'=>'Ir al siguiente.');
		$toolbar[] = array('id'=>"${id}ultimo", 'image'=>'ultimo', 'description'=>'Ir al último.');
*/	}
	
	public function setobjectid ($id){$this->objectid=$id;}
	
	//TODO Diferenciar las fichas originales de las que están soportadas por el programa.
	
	public function doaction($action, $data, &$tabledef, &$recs){
		switch ($action['id']) {
/*			case 'nuevo':
				//TODO si existe el formulario frm_$idnuevo se llama sino se crea un card con los campos del tabledef que llame a $id_insert
				if (class_exists("frm_{$this->objectid}nuevo")) {
					return array('open', "frm_{$this->objectid}nuevo");
				}
				break;
				
			case 'editar':
				if (class_exists("frm_{$this->objectid}editar")) {
					$ret = array('open', "frm_{$this->objectid}editar", 'seek',	$tabledef->getrecordkey());
					return $ret;
				}
				break;
				
			case 'borrar':
				if (class_exists("frm_{$this->objectid}borrar")) {
					$ret = array('open', "frm_{$this->objectid}borrar", 'seek',	$tabledef->getrecordkey());
					return $ret;
				}
				break;
*/				
			case 'buscar':
				break;
			case 'findrecord':
				break;
				
				
			case 'primero':
				$tabledef->go('first');
				break;
			case 'anterior':
				$tabledef->go('previous');
				break;
			case 'siguiente':
				$tabledef->go('next');
				break;
			case 'ultimo':
				$tabledef->go('last');
				break;
				
			case 'lookup':
				foreach(array_keys($recs['current']) as $key){
					if (isset($data[$key])) $recs['current'][$key] = $data[$key];
				}
				return (array('open',"frm_${data['lookup']}lookup", "seek", $tabledef->getautokeyfilter()));
				
			case 'seek':
				if (method_exists($tabledef, 'setkeyfromrecord')){
					$tabledef->setkeyfromrecord($data);
					$recs['current'] = $tabledef->getrecordfromkey($recs['exists']);
					$recs['previous'] = $recs['current'];
				}
				break;
				
			case 'setvalues':
				foreach(array_keys($recs['current']) as $key){
					if (isset($data[$key])) $recs['current'][$key] = $data[$key];
				}
				break;
				
		}
		
		return false;
		
	}
	
/*
 * 
 *		PARTE A PONER EN EL DOACTION DEL FORMULARIO QUE LO UTILICE (EN PRINCIPIO OBSOLETO POR SU COMPLICACIÓN).
 * 
 * 
  		if (isset($this->actions[$action])) {
			switch ($this->actions[$action]['group']){
				case 'card': 
					if ($ret = $this->card->doaction($this->actions[$action], $data, $this->tbdef, $this->recs)) return $ret;
					break;
					
			}
		}
*/
	
	
	
	
	
	
	
}
?>
