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
 * Genera una lista de acciones
 * @package html
 */
class bas_htm_buttonbar {
	private $actions, $id;
	
	function __construct($actions, $id=''){
		$this->actions =& $actions;
		$this->id = $id;
	}
	
	function printme(){
		global $CONFIG;
		
		echo "<div class=\"buttonbar\"";
		if ($this->id) echo " id=\"$this->id\"";
		echo ">\n";
		
		foreach($this->actions as $action){
			$bt = new bas_htm_tptaction($action); 
			echo $bt->stamp();
		}
			
		echo "</div>\n";
	}

}
?>
