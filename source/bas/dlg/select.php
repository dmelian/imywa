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
class bas_dlg_select{
	protected $tbdef;
	protected $actions = array('aceptar','cancelar');
	protected $cols = array();
	protected $returnid='';
	
	public function __construct(){
		$this->tbdef = new bas_dat_tabledef();
		$this->tbdef->addcol('','selected'); 
		$this->tbdef->setproperty('dynamic_name', "\${rec['id']}");
		$this->tbdef->settemplate('checkbox'); 
		#$this->tbdef->addcol('Id');
		$this->tbdef->addcol('Columna','caption');
		$this->tbdef->addcol('', 'subir');
		$this->tbdef->setproperty('dynamic_value', "addhidden('action','subir'); addhidden('orderid','\${rec['id']}'); submit();");
		$this->tbdef->settemplate('button'); 
		$this->tbdef->addcol('', 'bajar');
		$this->tbdef->setproperty('dynamic_value', "addhidden('action','bajar'); addhidden('orderid','\${rec['id']}'); submit();");
		$this->tbdef->settemplate('button'); 
		
	}
	
	public function OnPaint(){
		$frm = new bas_htm_form("SELECCIÓN DE CAMPOS",$this->actions);
		$ds = new bas_dat_arraydataset($this->cols);
		$tb = new bas_htm_table($this->tbdef, $ds);
		$frm->add($tb);
		$frm->printme();
	}
/*
	private function swaporder($o1, $o2){
		$tmp = $this->cols[$o2];
		$this->cols[$o2] = $this->cols[$o1];
		$this->cols[$o1] = $tmp;
	}
*/	

	private function swaporder($id1, $id2){
		$newcols= array();
		foreach($this->cols as $id => $col){
			if ($id == $id1 || $id == $id2){
				if (isset($tmp)) {$newcols[$id]= $col; $newcols[$tmpid]= $tmp;}
				else {$tmp= $col; $tmpid= $id;} 
			} else $newcols[$id] = $col;
		}
		unset($this->cols);
		$this->cols= $newcols;
	}

		
	private function updateselected($data){
		$order=1;
		foreach(array_keys($this->cols) as $id) {
			$this->cols[$id]['selected'] = isset($data[$id]);
			$this->cols[$id]['order']= $order++;
		}
	}
	
	public function OnAction($action, $data){
		switch($action){
/*			case 'settabledef': //OBSOLETO
				$this->cols = $data->getselectinfo();
				break;
*/
			case 'initdlgselect':
				$this->cols = $data['cols'];
				$this->returnid = isset($data['id']) ? $data['id']: '';
				break;
				
			case 'aceptar':
				$this->updateselected($data);
				return array('return',$this->returnid.'selectset',$this->cols);
				
			case 'cancelar':
				return array('close');
/*				
			case 'subir':
				$this->updateselected($data);
				foreach($this->cols as $ix => $col) {
					if ($data['orderid'] == $col['id']){
						if ($ix>0) { $this->swaporder($ix, $ix-1); break;}
					}
				}
				break;
				
			case 'bajar':
				$this->updateselected($data);
				foreach($this->cols as $ix => $col) {
					if ($data['orderid'] == $col['id']){
						if ($ix < count($this->cols)-1) { $this->swaporder($ix, $ix+1); break;}
					}
				}
				break;
*/				
			case 'subir': case 'bajar':
				$this->updateselected($data);
				$keys= array_keys($this->cols);
				$order= array_search($data['orderid'],$keys);
				if (($action=='subir') && ($order>0)) { $this->swaporder($data['orderid'], $keys[$order -1]); }
				if (($action=='bajar') && ($order<count($keys)-1)) { $this->swaporder($data['orderid'], $keys[$order +1]); }
				$keys= array_keys($this->cols);
				break;
				
		}
	}
}
?>
