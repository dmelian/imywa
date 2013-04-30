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
class bas_dlg_filter{
	protected $tbdef;
	protected $actions = array('aceptar','cancelar','quitarfiltros');
	protected $filters = array();
	protected $returnid;
	
	public function __construct(){
		$this->tbdef = new bas_dat_tabledef();
		#$this->tbdef->addcol('Id');
		$this->tbdef->addcol('Campo', 'caption');
		$this->tbdef->addcol('Filtro', 'filter');
		$this->tbdef->setproperty('dynamic_name', "\${rec['id']}");
		$this->tbdef->settemplate('input');
		
	}
	
	public function OnPaint(){
		$frm = new bas_htm_form("FILTROS",$this->actions);
		$ds = new bas_dat_arraydataset($this->filters);
		$tb = new bas_htm_table($this->tbdef, $ds);
		$frm->add($tb);
		$frm->printme();
	}
	
	private function updatefilters($data){
		foreach(array_keys($this->filters) as $id) {
			$this->filters[$id]['filter'] = $data[$id];
		}
		$this->lastdata = $data;
	}
	
	public function OnAction($action, $data){
		switch($action){
			case 'initdlgfilter':
				$this->filters = $data;
				break;
				
/*			case 'initdlgselect':
				$this->cols = $data['tabledef']->getselectinfo();
				$this->returnid = $data['id'];
				break;
*/				
			case 'aceptar':
				$this->updatefilters($data);
				return array('return',$this->returnid.'filterset',$this->filters);
				
			case 'cancelar':
				return array('close');
				
			case 'quitarfiltros':
				$this->updatefilters(array());
				break;
				
		}
	}
}
?>
