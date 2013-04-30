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
 * Tabla Html.
 * @package html
 */
class bas_htm_tablelandscape{
	private $tabledef;
	private $rowtbdef;
	private $dataset;
	
	public function __construct(&$tabledef, &$rowtabledef, &$dataset){
		$this->tabledef =& $tabledef;
		$this->rowtbdef =& $rowtabledef;
		$this->dataset =& $dataset;
	}
	
	public function printme(){
		
		echo "<table>\n";
		$coldefs = $this->tabledef->getcols();
		$rowdefs = $this->rowtbdef->getcols();
		
		if ($this->tabledef->hashead){
			echo "<thead>\n";
			echo "<tr class=rowhead>\n";
			foreach($coldefs as $col) {
				$data = $col['caption'];
				if (isset($col['headtemplate'])) eval ("\$data=\"${col['headtemplate']}\";");
				echo "<td>$data</td>\n";
			}
			echo "</tr>\n";
			echo "</thead>\n";
		}
		
		if ($this->tabledef->hasfoot) {
			echo "<tfoot>\n";
			echo "<tr class=rowfoot>\n";
			foreach($coldefs as $col) {
				$data = $col['foot'];
				if (isset($col['foottemplate'])) eval ("\$data=\"${col['foottemplate']}\";");
				echo "<td>$data</td>\n";
			}
			echo "</tr>\n";
			echo "</tfoot>\n";
		}
		
		echo "<tbody>\n";		
		
		$rec = $this->dataset->reset();
		
		$nrow = 0;
		while ($rec){
			$row = $rowdefs[$rec['rowid']]; //El dataset tiene que tener el campo rowid, que especifique el tipo de fila que se trata.
			
			$rowcls = $nrow++ % 2 == 0 ? "row2n": "row2n1";				
			echo "<tr class=\"$rowcls\">\n";
			$colspan = 0;
			foreach($coldefs as $col) {
				if ($colspan == 0){
					$data = $rec[$col['id']];
					if (isset($row['template'])) {$data = $row['template']->stamp($data, $rec);} #eval ("\$data=\"${col['template']}\";");
					elseif (isset($col['template'])) {$data = $col['template']->stamp($data, $rec);}
					if (isset($row['colspan'])) {
						$colspan = $row['colspan'];
						echo "<td colspan=\"$colspan\">";
						$colspan--;
					} else echo "<td>"; 
					echo "$data</td>\n";
				} else $colspan--; 
			}
			// TODO: Meter los datos de las columnas del detalle
			echo "</tr>\n";
			$rec=$this->dataset->next();
		}	
		echo "</tbody>\n";
		
		echo "</table>\n";
			
	}
}
?>
