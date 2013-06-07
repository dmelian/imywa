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
class bas_frm_list{
	protected $tbdef;
	protected $actions=array();
	protected $toolbar=array();
	protected $header;
	protected $footer;
	private $list;
	
	public function __construct(){
		$this->list = new bas_act_list($this->actions, $this->toolbar);
		$this->header = new bas_htm_elements(); 
		$this->footer = new bas_htm_elements(); 
	}

	
	public function OnPaint(){
		
		$this->tbdef->refresh();
		
		$frm = new bas_htm_form("LISTA DE ".strtoupper($this->tbdef->caption), $this->toolbar);
		
		$frm->opendiv('list_head');
		// Cabecera de filtros automáticos
		$cols = $this->tbdef->getcolsbyproperty('keyfiltered',true);
		$countcols = count($cols);
		if ($countcols){
			$frm->opendiv('autofilters');
			$fdef = new bas_dat_tabledef();
			$i=0;
			foreach($cols as $col){
				$i++;
				$fdef->addcol($col['caption'],$col['id']); 
				if (isset($col['lookup'])) $fdef->setproperty('lookup',$col['lookup']);
				elseif ($i == $countcols) $fdef->setproperty('lookup');
				$fdef->settemplate('hidden');
			}
			$autofilter = $this->tbdef->getautokeyfilter();
			$card = new bas_htm_card($fdef, $autofilter);
			$frm->add($card);
			$frm->closediv();
		}
		// Cabecera del formulario
		$frm->add($this->header);
		$frm->closediv(); //list_head
		
		// Detalle de la query.
		$frm->opendiv('list_body');
		$dataset = new bas_sql_myqrydataset($this->tbdef);
		$table = new bas_htm_table($this->tbdef, $dataset);
		$frm->add($table);
		$frm->closediv(); //list_body
		
		// Pie
		$frm->opendiv('list_foot');
		$frm->add($this->footer);
		$frm->closediv();
		
		$frm->printme();
	}
	
	public function OnAction($action, $data){
		if (isset($data['selected'])){
			$this->tbdef->setkeyselected($data);			
		}
		if (isset($this->actions[$action])) {
			switch ($this->actions[$action]['group']){
				case 'list': 
					if ($ret = $this->list->doaction($this->actions[$action], $data, $this->tbdef)) return $ret;
					break;
					
			}
		}
	}
	
}
?>
