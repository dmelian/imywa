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
class bas_act_navigate{
	
	public function __construct(&$actions, &$toolbar, $id=''){
		$actions["${id}primero"] = array('id'=>'primero', 'group'=>"${id}navigate");
		$actions["${id}anterior"] = array('id'=>'anterior', 'group'=>"${id}navigate");
		$actions["${id}siguiente"] = array('id'=>'siguiente', 'group'=>"${id}navigate");
		$actions["${id}ultimo"] = array('id'=>'ultimo', 'group'=>"${id}navigate");
		
		$toolbar[] = array('id'=>"${id}primero", 'image'=>'primero', 'description'=>'Ir al primero.');
		$toolbar[] = array('id'=>"${id}anterior", 'image'=>'anterior', 'description'=>'Ir al anterior.');
		$toolbar[] = array('id'=>"${id}siguiente", 'image'=>'siguiente', 'description'=>'Ir al siguiente.');
		$toolbar[] = array('id'=>"${id}ultimo", 'image'=>'ultimo', 'description'=>'Ir al último.');
	}

	public function doaction($action, $data, &$tabledef){
		
		switch ($action['id']) {
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
		}
		return false;
		
	}
	
}

?>
