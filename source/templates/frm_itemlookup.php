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
class frm_itemlookup extends bas_frm_list {
	
	public function __construct(){
		parent::__construct();
		$this->tbdef = new bas_sql_querydef('Item');
		$this->tbdef->add('item');
		$this->tbdef->addcol('Sel',"selected");
		$this->tbdef->setproperty('system'); 
		$this->tbdef->setproperty('aliasof','item');
		$this->tbdef->settemplate('radio');
		$this->tbdef->addcol('item');
		$this->tbdef->addcol('');
		$this->tbdef->addcol('');
		$this->tbdef->addcol('');
		$this->tbdef->addcol('');
		$this->tbdef->addcol('');
		array_push ($this->toolbar, 'nueva', 'aceptar', 'cancelar');
	}
	
	public function OnAction($action, $data){
		switch ($action){
			case 'aceptar':
				return array('return', 'setvalues', array('item' => $data['selected']));
				
			case 'cancelar':
				return array('close');
				
			case 'nueva':
				return array ('switch', 'frm_itemnuevo');
				
			case 'setvalues':
				if ($data['item']) return array('return', 'setvalues', array('item' => $data['item']));
				break;
				
			default: 
				$ret = parent::OnAction($action,$data);
				if (isset($ret)) return $ret;
		}
	}
	
}
?>
