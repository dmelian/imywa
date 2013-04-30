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
class bas_frm_card{
	public $id;
	protected $tbdef;
	protected $header;
	protected $subforms=array();
	protected $footer;
	protected $toolbar=array();
	protected $recs; //current, previous, exists
	private $card;
	protected $lookup;
	protected $title;
	
	public function __construct($id = '', $title = null){
		$this->id = $id;
		$this->recs = array();
		$this->recs['exists'] = false;
		$this->header = new bas_htm_elements(); 
		$this->footer = new bas_htm_elements(); 
		$this->title = $title;
		
	}

	public function addsubform($frm){
		$this->subforms[$frm->id] = $frm;
	}	
	
	public function OnLoad(){
		foreach($this->subforms as $frm) if (method_exists($frm, 'OnLoad')) $frm->OnLoad();
	}
	
	public function OnPaint(&$form = false){
/*		$ds = new bas_sql_myqrydataset($this->tbdef);
		$data = $ds->reset();
		$this->tbdef->setkeyfromrecord($data);
*/		
		$createform = ! is_object($form);
		$title = is_null($this->title)?  "FICHA DE ".strtoupper($this->tbdef->caption) : $this->title;
		if ($createform) $frm = new bas_htm_form($title, $this->toolbar); 
		else $frm =& $form;

		// Cabecera
		$frm->opendiv('card_head');
		$frm->add($this->header);
		$frm->closediv();
		
		$tabsdef = $this->tbdef->gettabs();
		if (count($tabsdef)){
			$tabs = new bas_htm_tabs();
			foreach($tabsdef as $tab){		
				$tabs->addtab($tab);
				$card = new bas_htm_card($this->tbdef->getcols($tab), $this->recs['current']);
				$tabs->add($card);
			}
			$frm->add($tabs);
		} else {
			$card = new bas_htm_card($this->tbdef, $this->recs['current']);
			$frm->add($card);
		}

		// Subformularios
		foreach($this->subforms as $subform) $subform->OnPaint($frm);

		// Pie
		$frm->opendiv('card_foot');
		$frm->add($this->footer);
		$frm->closediv();
		
		
		if ($createform) $frm->printme();
	}
	
	protected function saverecord($data){
		foreach(array_keys($this->recs['current']) as $key){
			if (isset($data[$key])) $this->recs['current'][$key] = $data[$key];
		}
	}
	
	protected function preparedataforsql(&$data){
		//Procesa el array data con los datos imputados por el usuario, para meterlos en la base de datos.
		$format = new bas_dat_format();
		foreach ($this->tbdef->getcols('*') as $col) if (isset($data[$col['id']])) {
			if (isset($col['format'])) $data[$col['id']] = $format->validate($data[$col['id']], $col['format']); 
		}
	}
	
	protected function initrecs($rec){
		$this->recs['exists'] = true;
		$this->recs['current'] = $rec;
		$this->recs['previous'] = $this->recs['current'];
	}
	
	public function OnAction($action, $data){
		
		if (strpos($action,'#') !== false){
			list($id, $action) = explode('#',$action);
			$this->subforms[$id]->OnAction($action, $data);
			
		} else {
		
			switch($action){
			
				case 'lookup':
					$this->saverecord($data);
					$this->lookup = $data['lookup'];
					foreach ($this->tbdef->getcolsbyproperty('lookup',$data['lookup']) as $col){
						if (isset($col['lookupfrm'])) $lookupfrm = "{$col['lookupfrm']}lookup";
						else $lookupfrm = "frm_${data['lookup']}lookup"; 
	
						if (isset($col['lookupfields'])){
							$lookupfrmseek = array();
							foreach($col['lookupfields'] as $field => $replacedfield) {
								$lookupfrmseek[$field] = isset($this->recs['current'][$replacedfield]) ? $this->recs['current'][$replacedfield] : '';  								
							}
						}
					}
					if (!isset($lookupfrm)) $lookupfrm = "frm_${data['lookup']}lookup"; // se quita el frm_
					if (!isset($lookupfrmseek) && method_exists($this->tbdef, 'getautokeyfilter')) {
						$lookupfrmseek = $this->tbdef->getautokeyfilter();
					}
					if (!isset($lookupfrmseek)) return array('open', $lookupfrm);
					else return (array('open',$lookupfrm, "seek", $lookupfrmseek));
					
				case 'seek':
					if (method_exists($this->tbdef, 'setkeyfromrecord')){
						$this->tbdef->setkeyfromrecord($data);
						$this->recs['current'] = $this->tbdef->getrecordfromkey($this->recs['exists']);
						$this->recs['previous'] = $this->recs['current'];
					}
					break;
					
				case 'setvalues':
					$saved = false;
					$MISCOLS = $this->tbdef->getcolsbyproperty('lookup',$this->lookup);
					foreach ($MISCOLS as $col){
						if (isset($col['lookupfields'])){
							foreach($col['lookupfields'] as $field => $replacedfield) {
								if (isset($data[$field])) $this->recs['current'][$replacedfield] = $data[$field];
							}
							$saved = true;
						}
					}
					if (!$saved) $this->saverecord($data);
					break;
			}	
		}
	}
	
}
?>
