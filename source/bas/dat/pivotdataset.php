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
class bas_dat_pivotdataset{
	private $baseds;
	private $pivotid;
	private $colsname;
	private $agfunc;
	private $lastbaserec;
		
	public function __construct($dataset, $pivotid, $colsname ='', $agfunc ='replace'){
		$this->baseds =& $dataset;
		$this->pivotid;
		if (! $colsname) $colsname = "$pivotid {\$rec['$pivotid']}";
		$this->colsname = $colsname;
		$this->agfunc = agfunc;		
	}
	
	
	public function reset(){
		$this->lastbaserec = $this->baseds->reset();
		return $this->completecurrec();
	}
	
	public function next(){
		return $this->completecurrec();
	}
	
	public function close(){
		$this->baseds->close();
	}
	
	private function completecurrec(){
		function readpivotfield($pivotrec, $rec, $value){
			$pivotid = $this->pivotid;
			eval ("\$colname = \"{$this->colsname}\";");
			switch($this->agfunc){
				case 'sum': if (isset($pivotrec[$colsname])) {$pivotrec[$colsname] += $value; break;}
				case 'replace': default:
					$pivotrec[$colname] = $value; break;
			}
		}
	
		if ($this->lastbaserec){
			$pivotrec = array();
			foreach($this->lastbaserec as $id => $value){
				if($id == $this->pivotid) readpivotfield($pivotrec, $this->lastbaserec, $value);
				else $pivotrec[$id]=$value;
			}
			while ($this->lastbaserec = $this->baseds->next()){
				$equal = true;
				foreach($this->lastbaserec as $id => $value){
					if($id != $this->pivotid) $equal= $pivotrec[$id] == $value;
					if (!$equal) break;
				}
				if ($equal)	readpivotfield($pivotrec, $this->lastbaserec, $this->lastbaserec[$this->pivotid]);
				else break;
			}			
			return $rec;
		} else return false;
	}
	
	
	//TODO: Algo como locate, seek o find. Por número de fila o por contenido de los campos.
	//Para el acceso a datos está implementado en el obs_sql_mydataset
	
}
?>
