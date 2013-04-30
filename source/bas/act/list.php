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
class bas_act_list{
	private $id;
	
	public function __construct(&$actions, &$toolbar, $id=''){
		global $CONFIG;
		if ($CONFIG['LSTTYPE'] == constant('LT_PAGE_LIST')){
			$actions["${id}primero"] = array('id'=>'primero', 'group'=>"${id}list");
			$actions["${id}anterior"] = array('id'=>'anterior', 'group'=>"${id}list");
			$actions["${id}siguiente"] = array('id'=>'siguiente', 'group'=>"${id}list");
			$actions["${id}ultimo"] = array('id'=>'ultimo', 'group'=>"${id}list");
		}
		$actions["${id}mostrar"] = array('id'=>'mostrar', 'group'=>"${id}list");
		$actions["${id}selectset"] = array('id'=>'selectset', 'group'=>"${id}list");
		$actions["${id}filtros"] = array('id'=>'filtros', 'group'=>"${id}list");
		$actions["${id}filterset"] = array('id'=>'filterset', 'group'=>"${id}list");
		//$actions["${id}orden"] = array('id'=>'orden', 'group'=>"${id}list");
		//$actions["${id}orderset"] = array('id'=>'orderset', 'group'=>"${id}list");
		$actions["${id}lookup"] = array('id'=>'lookup', 'group'=>"${id}list");
		$actions["${id}seek"] = array('id'=>'seek', 'group'=>"${id}list");
		$actions["${id}setvalues"] = array('id'=>'setvalues', 'group'=>"${id}list");
		
		if ($CONFIG['LSTTYPE'] == constant('LT_PAGE_LIST')){
			$toolbar[] = array('id'=>"${id}primero", 'image'=>'primero', 'description'=>'Ir al primero.');
			$toolbar[] = array('id'=>"${id}anterior", 'image'=>'anterior', 'description'=>'Ir al anterior.');
			$toolbar[] = array('id'=>"${id}siguiente", 'image'=>'siguiente', 'description'=>'Ir al siguiente.');
			$toolbar[] = array('id'=>"${id}ultimo", 'image'=>'ultimo', 'description'=>'Ir al último.');
		}
		$toolbar[] = array('id'=>"${id}mostrar", 'image'=>'mostrar', 'description'=>'Seleccionar las columnas del listado.');
		$toolbar[] = array('id'=>"${id}filtros", 'image'=>'filtros', 'description'=>'Aplicar diferentes filtros por columnas.');
		//$toolbar[] = array('id'=>"${id}orden", 'image'=>'orden', 'description'=>'Ordenar el listados por alguna de sus columnas.');
		
		$this->id = $id;
	}
	
	public function doaction($action, $data, &$tabledef){
		switch ($action['id']) {
			case 'primero':
				$tabledef->go('first');
				break;
			case 'anterior':
				$tabledef->go('previouspage');
				break;
			case 'siguiente':
				$tabledef->go('nextpage');
				break;
			case 'ultimo':
				$tabledef->go('last');
				break;
				
			case 'mostrar':
				return array('open', 'bas_dlg_select', 'initdlgselect', array('cols'=>$tabledef->getcols('*'),'id'=>$this->id));
			case 'selectset':
				
				$selected = $order = array();
				foreach($data as $col){
					$order[] = $col['id'];
					if ($col['selected']) $selected[] = $col['id'];
				}
				
				$tabledef->setcolorder($order);
				$tabledef->select(false, '*');
				$tabledef->select(true, $selected);
				break;
				
			case 'filtros':
				return array('open', 'bas_dlg_filter', 'initdlgfilter', $tabledef->getcols('*'));
				
			case 'filterset':
				foreach($data as $filterdef){
					$tabledef->setfilter($filterdef['filter'], $filterdef['id']);
				}
				break;
				
			case 'orden': case 'orderset':
				break;

				
			// Para el autokeyfilter en las listas.	
			case 'lookup':
				return (array('open',"frm_${data['lookup']}lookup", "seek", $tabledef->getautokeyfilter()));
				
			case 'seek': case 'setvalues':
				$tabledef->setkeyfromrecord($data);
				break;
				
		}
		
		return false;
		
	}
	
	
}
?>
