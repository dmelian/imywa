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


class bas_sqlx_unionquery{
	public $querys; // private o protected ¿?
	public $current;
	public $original;
	protected $pos_current, $size;
	protected $key;
	
	public function __construct($queryObj){
	    $this->querys[] = $queryObj;
	}
	
	public function addQuery($queryObj){
	    $this->querys[] = $queryObj;
	}
	
	public function getMaindb(){
		return $this->querys[0]->getMaindb();
	}
	
	public function getAllfield(){
		return $this->querys[0]->getAllfield();
	}
	
	public function setfilter($value,$id=""){
// 		foreach($this->querys as $element){
// 			$element->setfilter($value,$id);
// 		}
		foreach($this->querys as $qry){
			$qry->setfilter($value,$id);
		}
// 		return $this->querys[0]->setfilter($value,$id);
	}
	
	public function setfilterRecord($record){
		foreach($this->querys as $qry){
			$qry->setfilterRecord($record);
		}
	}
	
	public function getfilter($id){
		return $this->querys[0]->getfilter($id);
	}
	
	public function setorder($order){
		$this->querys[0]->setorder($order);
	}
	
	public function query(){
		$clause = "";
		$sep = "";
		foreach($this->querys as $qry){ // recorremos las distintas query concatenandolas, generando la consulta final
			$clause = $clause . $sep. "(". $qry->query(false).")";
			$sep = " UNION ";
		}
		$clause = $clause . $this->querys[0]->orderclause();
		return $clause;
	}
	
	public function setAttr($attr,$value){
		foreach($this->querys as $qry){
			$qry->setAttr($attr,$value);
		}
	}
	
	public function existField($id){
		return $this->querys[0]->existField($id);
	}	
	
	public function  getField($id){
		return $this->querys[0]->getField($id);
	}
	
	public function getautokeyRecord($record){ // $record = asociative vector
		return $this->querys[0]->getautokeyRecord($record);
	}
	
    public function getfilters(){
        return $this->querys[0]->getfilters();
    }
	
}

?>