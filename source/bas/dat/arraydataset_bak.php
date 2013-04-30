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
	
	public function reset(){return reset($this->data);}
	
	public function next(){return next($this->data);}
	
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
