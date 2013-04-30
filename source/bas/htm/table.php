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
class bas_htm_table{
	private $tabledef;
	private $dataset;
	private $extension;
	
	public function __construct(&$tabledef, &$dataset){
		$this->tabledef =& $tabledef;
		$this->dataset =& $dataset;
		$this->extension = array('*');
	}
	
	public function preextend(&$extension){array_unshift($this->extension, $extension);}
	public function postextend(&$extension){array_push($this->extension, $extension);}
	
	public function printme(){
		
		if ($this->dataset->errormsg){
			echo "<p>Error en el dataset<br>\n";
			echo "Error: ".$this->dataset->errormsg."</p>\n";
			
		} else {
		
			echo "<table>\n";
			
			//TODO: Rehacer los templates de la cabecera y pie.
			$cols=array();
			foreach($this->extension as $extension){
				if ($extension == '*') $cols = array_merge($cols, $this->tabledef->getcols());
				else $cols = array_merge($cols, $extension->getcols());
			}
			if ($this->tabledef->hashead){
				echo "<thead>\n";
				echo "<tr class=rowhead>\n";
				foreach($cols as $col) {
					$data = $col['caption'];
					if (isset($col['headtemplate'])) eval ("\$data=\"${col['headtemplate']}\";");
					$tdmod='';
					if (isset($col['colwidth'])) $tdmod .= " width=\"{$col['colwidth']}\"";
					echo "<th$tdmod>$data</th>\n";
				}
				echo "</tr>\n";
				echo "</thead>\n";
			}
			
			if ($this->tabledef->hasfoot) {
				echo "<tfoot>\n";
				echo "<tr class=rowfoot>\n";
				foreach($cols as $col) {
					$data = $col['foot'];
					if (isset($col['foottemplate'])) eval ("\$data=\"${col['foottemplate']}\";");
					$tdmod='';
					if (isset($col['colwidth'])) $tdmod .= " width=\"{$col['colwidth']}\"";
					echo "<td$tdmod>$data</td>\n";
				}
				echo "</tr>\n";
				echo "</tfoot>\n";
			}
			
			echo "<tbody>\n";

			$template = new bas_htm_template();
			
			$rec = $this->dataset->reset();
			if (method_exists($this->tabledef, 'getrecordkey'))	$currkey = $this->tabledef->getrecordkey();
			else $currkey = false;
			//TODO: PONER EL SPANROW
			$nrow = 0;
			while ($rec){
				foreach($this->extension as $extension){
					if ($extension != '*') $extension->mergedata($rec);
				}
				$nrow++;
				if ($rowcls = $this->tabledef->getrowproperty('dynamic_class')) $rowcls=$rec[$rowcls];
				if (!$rowcls) $rowcls = $nrow % 2 == 0 ? "row2n": "row2n1";
				
				echo "<tr class=\"$rowcls\">\n";
				$colspan = 0;
				foreach($cols as $col) {
					if ($colspan == 0 ){
						$rowid = $this->tabledef->getrowid();
						$rowid = $rowid ? $rec[$rowid] : $nrow;
						// <TD>
						$tdmod = '';
						$span = $this->tabledef->getspan($rowid, $col['id']);  
						if ($span){
							$colspan = $span['length']; // de momento sólo utilizamos colspan. (falta por implementar el rowspam) 
							$tdmod .= " colspan=\"$colspan\"";
							$colspan--;
						}
						if (isset($col['align'])) {
							$align = new bas_htm_tdalign($col['align']);
							$tdmod .= ' ' . $align->htmltext;
						} elseif (isset($col['dynamic_align'])) {
							eval("\$align = new bas_htm_tdalign(\"{$col['dynamic_align']}\");");
							$tdmod .= ' ' . $align->htmltext;							
						}
						if (isset($col['colwidth'])) $tdmod .= " width=\"{$col['colwidth']}\"";
						echo "<td$tdmod>";
						
						$data = isset($rec[$col['id']]) ? $rec[$col['id']]: '';
						if (isset($col['template'])) {
							$data = $template->stamp($this->tabledef, $col['id'], $rec);
						} elseif (isset($col['format'])) {
							$format = new bas_dat_format($col['format']);
							$data = $format->format($data);
						} elseif (isset($col['dynamic_format'])){
							eval("\$format = new bas_dat_format(\"{$col['dynamic_format']}\");");
							$data = $format->format($data);
						}
						  
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
}
?>
