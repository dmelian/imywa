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


class bas_sqlx_record extends bas_sqlx_datapointer{
	public $first_item,$last_item;	
	
	private function save_state(){
	    $pos = count($this->current);
	    $this->first_item = $this->current[0];
	    $this->last_item = $this->current[$pos-1];
	}
	public function first(){
	    //return $this->createQueryProx(true);
	    $this->first_pos(10);
	    $this->save_state();
	}
	
	public function last(){
	    //return $this->createQueryProx(true);
	    $this->last_pos(10);
	    $this->save_state();
	}


	public function next(){
	    //return $this->createQueryProx(true);
	    $this->prox_next($this->current,10);
	    $this->save_state();
	}
	
	public function previous(){
	    $this->prox_previous($this->current,10);
	    $this->save_state();
	}
}

?>
