<?php
/*
 Copyright 2009-2012 Domingo Melian, Yeray Pérez

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

class bas_csv_list{

	private $tabledef;
	private $dataset;
	private $extension;
	
	public function __construct(&$tabledef, &$dataset){
		$this->tabledef =& $tabledef;
		$this->dataset =& $dataset;
		$this->extension = array('*');
	}
	
	public function preextend(&$extension){
		array_unshift($this->extension, &$extension);
	}
	public function postextend(&$extension){
		array_push($this->extension, &$extension);
	}
	
	public function csvme($name=''){
		if (!$name) $name='output.csv';
		header('Content-Type: text/csv');
		header('Content-Disposition: inline; filename="'.$name.'"');
		header('Cache-Control: private, max-age=0, must-revalidate');
		header('Pragma: public');

		$cols=array();
		foreach($this->extension as $extension){
			if ($extension == '*') $cols = array_merge($cols, $this->tabledef->getcols());
			else $cols = array_merge($cols, $extension->getcols());
		}

		//cabeceras de la tabla.
			
		if ($this->tabledef->hashead){
			foreach($cols as $col) {
				echo $col['caption']."\t";
			}
			echo "\n";
		}
		
		//contenido del dataset.		
		$rec = $this->dataset->reset();	
		
		while ($rec){
			foreach($cols as $col) {
				echo $rec[$col['id']];
				echo "\t";
			}
			echo "\n";
			$rec=$this->dataset->next();
		}

	}
}

?>
