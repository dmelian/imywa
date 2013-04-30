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


class bas_sqlx_dataview extends bas_sqlx_datapointer{
	public $first_item,$last_item;
	public $width;
	private $selected;
	
	public function first(){ // Utilizamos el tamaño 10 para estas pruebas.
	    $this->first_pos($this->width);
	}
	
	public function last(){
	    $this->last_pos($this->width);
	}

	public function next(){
	    $this->prox_next($this->width,($this->pos_current+1));

	}
	
	public function previous(){
	    $this->prox_previous($this->width,($this->pos_current-1));
	}
	
	public function recorcount(){
	    return $this->size;
	}
	
	public function SetViewWidth($width){
		if ($width==-1)$width = $this->size;
	    $this->width = $width;
	}
	
	public function SetViewPos($pos){
	    $this->data_posfile($pos,$this->width);
	}
	
	public function get_View(){
	    return $this->current;
	}
	
	public function vec_fields(){ // Nos genera un vector con todos los caption de los objetos fields visibles.
	    $vec='';
	    $pos = 0;
	    foreach($this->query->fields as $field => $value){
			if($value["fieldtype"]->visible){
				$vec[$pos] = $value["fieldtype"]->caption;
				$pos++;
			}
	    }
	    return $vec;
	}	
	
	public function Allrows(){
		return $this->acces_posfile(0,$this->size);
	
	}
	
	//   Selección del elemento elegido.
	public function setSelected($pos){
		if ($this->current){		   
			$real_pos = $this->pos_current+$pos;
// 		    $row = $this->current[$real_pos];
		    $row = $this->get_dataRow($real_pos);
	 	    $this->selected = $row; // ### Apaño
	 	    	 	    
	 	    $this->selected['pos'] = $real_pos;
		    return true;
		    
		} else {
			$this->selected= null;
			return false;
		} 
	}
	
	public function getSelected(){
	    if (isset($this->selected)){
	      $row_selected = $this->selected;
	      unset($row_selected['pos']);  // ###
	      return $row_selected;
	    }
	    return NULL;
	}
	
	public function selectedPosRelative(){
		if (isset($this->selected["pos"]))		return ($this->selected["pos"] - $this->pos_current)+1;
		else return -1; // no se ha seleccionado ningún registro todavía.
		
	}
	
	public function existSelected(){
	    return isset($this->selected);
	}
	
	public function getQuerySize(){
		return $this->size;
	}
}

?>
