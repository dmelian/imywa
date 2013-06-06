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
 * Conjunto de datos de consultas mysql
 * @package sql
 */
class bas_sql_myqrydataset {
	private $result; 
	public $success;
	private $currec;
	private $curpivotrec;
	public $errormsg;
	private $pivot;
	private $emptypivotrec;
	private $currWalkType = '';
	
	public function __construct($datadef, $databasename='', $hostname='', $connection=false){
		global $_SESSION;
		global $_LOG;
		
		
		if (is_object($datadef)) {
			if (! method_exists($datadef, 'query')){
				$_LOG->log("Error. No se encuentra el método query en: ".print_r($datadef,true));
			}
			$query = $datadef->query();
			$this->pivot = $datadef->getpivotinfo();
			if ($this->pivot) $this->emptypivotrec = $datadef->getemptypivotrec();
		} else {
			$query = $datadef;
			$this->pivot = false;
		}
		
		//if (!$databasename) $databasename = $_SESSION->apps[$_SESSION->currentApp]->mainDb;
		//if (!$hostname) 	$hostname = $_SESSION->apps[$_SESSION->currentApp]->dbServer;
		if (($databasename =='') && ($hostname==''))$_SESSION->apps[$_SESSION->currentApp]->getMainDb($hostname, $databasename);
		//TODO: Si es una tabla la trasnformamos en una select *.
		
		if ($connection === false) {
			$privateconnection = true;
			$connection = mysqli_init();
			if (@$connection->real_connect($hostname, $_SESSION->user, $_SESSION->password, $databasename)){
				$connection->set_charset('utf8');
				$connectionopened = true;
			} else {
				$this->errormsg = $connection->connect_error;
				$_LOG->log("myqrydataset.error en conexión a host:$hostname, database:$databasename",1);
				$this->success = false;
				$connectionopened = false;
			}
		} else {
			$privateconnection = false;
			$connectionopened = true;
		}
				
		if ($connectionopened){
			$this->success = @$connection->real_query($query);
			if ($this->success){
				$_LOG->log("myqrydataset.success query: <$query>",10);
				$this->result = $connection->store_result();
				if (is_object($datadef)) {
					if (method_exists($datadef, 'setrowcount')) {
						if (@$connection->real_query('select found_rows()')){
							$ds = $connection->store_result();
							$r = $ds->fetch_row();
							$datadef->setrowcount($r['0']);
							$ds->close();
						}
					}
				}
			} else {
				$this->errormsg = $connection->error;
				$_LOG->log("myqrydataset.error $this->errormsg en query: <$query>",1);
			}
			if ($privateconnection) $connection->close();
		}
	}

	public function export(){
		$ret= array();
		if ($rec= $this->reset()) do $ret[]=$rec; while ($rec= $this->next());
		return $ret;
	}
	
	public function count(){
		if ($this->success) return $this->result->num_rows;
		else return 0;
	}
	
	public function reset($type = ''){
		global $_LOG;
		$this->currWalkType = $type ? $type : ($this->pivot ? 'pivot' : 'default');
		if ($this->success){
			$this->result->data_seek(0);
			$this->currec = $this->result->fetch_assoc();
			#$_LOG->log("reset. type={$this->currWalkType}");
			switch($this->currWalkType){
				
			case 'pivot':
				$this->completepivotrecord();
				return $this->curpivotrec;
				 
			default:
				return  $this->currec;
				
			}
		} else return false;
	}

	public function next(){
		global $_LOG;
		if ($this->success) {
			if ($this->currec){
				#$_LOG->log("next. type={$this->currWalkType}");
				switch($this->currWalkType){
					
				case 'pivot':
					$this->completepivotrecord();
					return $this->curpivotrec;
					 
				default:
					$this->currec = $this->result->fetch_assoc();
					return $this->currec;
					
				}
			} else return false;
		} else return false;
	}
	
	public function getcurrec() { 
		if ($this->pivot) return $this->curpivotrec; 
		else return  $this->currec;
	}
	
	public function value($id){
		if ($this->pivot) return $this->curpivotrec[$id]; 
		else return  $this->currec[$id];
	}
	
	public function close(){
		if ($this->success) $this->result->close();
	}
	
	public function locate($findrec, $ordereddataset = false){
	/*
	 * Localiza el registro que contenga los valores para los campos pasados en rec en todo el dataset.
	 * Devuelve el número del primero que lo contenga.
	 * (Y el dataset queda colocado en ese lugar).
	 * 
	 * TODO: Optimizar esto con busquedas dicotómicas si el dataset está ordenado.
	 * TODO: Modificar para que acepte el Pivot
	 */
		if ($this->success){
			$ix = 0; $found = false;
			$fields = array_keys($findrec);
			if ($this->result->data_seek($ix)){ 
				while ($rec = $this->result->fetch_assoc()){
					if ($found = $this->comparefields($fields, $findrec, $rec) == 0) break;
					$ix++;
				}
				if (is_null($rec)) $this->reset();
				else $this->currec = $rec;
				if ($found)	return $ix;
			}
		}
		return false;
	}

	public function comparefields($fields, $r1, $r2){
		foreach($fields as $field){
			if ($r1[$field] < $r2[$field]) $ret = -1; elseif ($r1[$field] == $r2[$field]) $ret = 0; else $ret = 1;
			if ($ret != 0) break;
		}
		return $ret;
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
	
	private function completepivotrecord(){
		if ($this->currec){
			$this->curpivotrec = $this->emptypivotrec;
			foreach($this->currec as $id => $value){
				if($id != $this->pivot['id'] && $id != $this->pivot['value']) $this->curpivotrec[$id]=$value;
			}
			$this->readpivotfield();
			while ($this->currec = $this->result->fetch_assoc()){
				$equal = true;
				foreach($this->currec as $id => $value){
					if($id != $this->pivot['id'] && $id != $this->pivot['value']) {
						if (is_null($value) || is_null($this->curpivotrec[$id])) $equal = is_null($value) && is_null($this->curpivotrec[$id]);
						else $equal = $this->curpivotrec[$id] == $value;
						if(!$equal) break;
					}
				}
				if ($equal)	$this->readpivotfield();
				else break;
			}			
		} else $this->curpivotrec = false;		
	}
	
	public function getpivotvalues(){
		if ($this->success && $this->pivot){
			$values = array();
			$this->result->data_seek(0);
			$rec = $this->result->fetch_assoc();
			if ($rec){
				$pivotrec = $this->emptypivotrec;
				foreach($rec as $id => $value){
					if($id != $this->pivot['id'] && $id != $this->pivot['value']) $pivotrec[$id]=$value;
				}
				$values[] = $rec[$this->pivot['id']];
				while ($rec = $this->result->fetch_assoc()){
					$equal = true;
					foreach($rec as $id => $value){
						if($id != $this->pivot['id'] && $id != $this->pivot['value']) {
							if (is_null($value) || is_null($pivotrec[$id])) $equal = is_null($value) && is_null($pivotrec[$id]);
							else $equal = $pivotrec[$id] == $value;
							if(!$equal) break;
						}
					}
					if ($equal)	$values[] = $rec[$this->pivot['id']];
					else break;
				}
				return $values;			
			} else return false;
		} else return false;
	}
	
	public function copy(){
		$result = new bas_dat_arraydataset();
		if ($this->success){
			$this->result->data_seek(0);
			$rec = $this->result->fetch_assoc();
		} else $rec = false;
		while ($rec){
			$result->add($rec);
			$rec = $this->result->fetch_assoc();		
		}
		return $result;
	}
		
		
	
}
?>
