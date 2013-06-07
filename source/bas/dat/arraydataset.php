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
 * Dataset simple implementado como array.
 * @package data
 */
class bas_dat_arraydataset{
	public $errormsg='';
	private $data;
	private $currec;
	private $curpivotrec;
	private $emptypivotrec;
	private $pivot;
		
	public function __construct($data = ''){
		if (is_object($data)) {
			$this->data = array();
			if ($r = $data->reset()) do {
				$this->data[] = $r; 
			} while ($r = $data->next());
			
		} else if (is_array($data)) {
			$this->data =& $data;
			
		} else $this->data = array();
	}
	
	public function count(){return count($this->data);}
	
	public function setpivotinfo($datadef){
		$this->pivot = $datadef->getpivotinfo();
		$this->emptypivotrec = $datadef->getemptypivotrec();
	}
	
	public function completepivotrecord(){
		if ($this->currec){
			$this->curpivotrec = $this->emptypivotrec;
			foreach($this->currec as $id => $value){
				if($id != $this->pivot['id'] && $id != $this->pivot['value']) $this->curpivotrec[$id]=$value;
			}
			$this->readpivotfield();
			while ($this->currec = next($this->data)){
				$equal = true;
				foreach($this->currec as $id => $value){
					if($id != $this->pivot['id'] && $id != $this->pivot['value']) {
						if (is_null($value) || is_null($this->curpivotrec[$id])) $equal = is_null($value) && is_null($this->curpivotrec[$id]);
						else $equal = $this->curpivotrec[$id] == $value;
						//$equal= $this->curpivotrec[$id] == $value;
						if(!$equal) break;
					}
				}
				if ($equal)	$this->readpivotfield();
				else break;
			}			
		} else $this->curpivotrec = false;		
		
	}
	
	
	private function readpivotfield(){
		$matchid = '';
		foreach($this->pivot['cols'] as $col){
			if ($col['matchvalue'] == $this->currec[$this->pivot['id']]) $matchid = $col['id'];
		}
		if (!$matchid) $matchid = $this->pivot['default'];
		switch($this->pivot['function']){
			case 'sum': 
				$this->curpivotrec[$matchid] += $this->currec[$this->pivot['value']]; 
				break;
			case 'replace': default:
				$this->curpivotrec[$matchid] = $this->currec[$this->pivot['value']];
		}
	}	
	
	
	
	public function reset(){
		if ($this->currec=reset($this->data)){
			if ($this->pivot) {
				$this->completepivotrecord();
				return $this->curpivotrec; 
			} else return  $this->currec;
		} else return false;
	}
	
	public function next(){
		if ($this->currec){
			if ($this->pivot) {
				$this->completepivotrecord();
				return $this->curpivotrec; 
			} else {
				$this->currec = next($this->data);
				return $this->currec;
			}
		} else return false;		
	}
	
	public function add($element){$this->data[]= $element;}
	
	public function write($element){
		if (current($this->data) === false) return false;
		else {$this->data[key($this->data)] = $element; return true;}
	}
	
	public function close(){}
	
	//TODO: Algo como locate, seek o find. Por número de fila o por contenido de los campos.
	//Para el acceso a datos está implementado en el obs_sql_mydataset
	
}
?>
