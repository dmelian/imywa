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

class frm_itemlista extends bas_frm_list {
	
	public function OnLoad(){
		
		array_push ($this->toolbar, 'ficha', 'nuevo', 'editar', 'borrar', 'salir'); //todo Poner la ficha, borrar y editar.
		
		$this->tbdef = new bas_sql_querydef('Item');
		
		$this->tbdef->add('itemtable');
		$this->tbdef->addcol('Sel',"selected");
		$this->tbdef->setproperty('system'); 
		$this->tbdef->setproperty('aliasof','item');
		$this->tbdef->settemplate('radio');
		$this->tbdef->addcol('');
		$this->tbdef->addcol('');
		$this->tbdef->addcol('');
		$this->tbdef->addcol('');
		$this->tbdef->addcol('');
		$this->tbdef->addcol('');
		$this->tbdef->addcol('');
		$this->tbdef->addcol('');
								
		
	}
	
	public function OnAction($action, $data){
		switch ($action){
			case 'salir':
				return(array('close'));
				
			case 'nuevo':
				return(array('open', 'frm_itemnuevo'));
				
			case 'editar':
				return array('open', 'frm_itemmodify', 'seek', $data['selected']);
				
			case 'borrar':
				return array('open', 'frm_itemdelete', 'seek', $data['selected']);
				
			case 'ficha':
				return array('open', 'frm_itemficha', 'seek', array('item' => $data['selected']));
				
			default:
				$ret = parent::OnAction($action,$data);
				if (isset($ret)) return $ret;
				
		}
	}
}
?>
