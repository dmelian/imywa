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
class bas_htm_list{
	/* Crea una tabla en formato especifico para el nuevo formulario de lista */
	
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
		
			echo "<div class=\"tableContainer\" id=\"formtable\">\n"; 
			echo "<div class=\"scrollTableView\"><div class=\"tableView\">\n"; 
			//echo "<table>\n";
			
			
			//TODO: Rehacer los templates de la cabecera y pie.
			$cols=array();
			foreach($this->extension as $extension){
				if ($extension == '*') $cols = array_merge($cols, $this->tabledef->getcols());
				else $cols = array_merge($cols, $extension->getcols());
			}
			if ($this->tabledef->hashead){
				echo "<div class=\"tableHeader\">\n"; //echo "<thead>\n";
				echo "\t<div class=\"tableRow\">"; //echo "<tr class=rowhead>\n";
				$icol = 0;
				foreach($cols as $col) {
					$data = $col['caption'];
					if (isset($col['headtemplate'])) eval ("\$data=\"${col['headtemplate']}\";");
					/* ColsWitdhs
					$tdmod='';
					if (isset($col['colwidth'])) $tdmod .= " width=\"{$col['colwidth']}\"";
					*/
					echo "<div class=\"tableCell\" id=\"col$icol\">$data</div>"; //echo "<th$tdmod>$data</th>\n";
					$icol++;
				}
				echo "</div>\n"; //echo "</tr>\n";
				echo "</div>\n"; //echo "</thead>\n";
			}
			
			echo "<div class=\"scrollTableContent\"><div class=\"tableContent\">\n"; //echo "<tbody>\n";

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
				if ($rowcls = $this->tabledef->getrowproperty('dynamic_class')) $rowcls=$rec[$rowcls];
				if (!$rowcls) $rowcls = $nrow % 2 == 0 ? "tableRow": "tableRowAlt";
				else $rowcls = "tableRow$rowcls";
				
				echo "\t<div class=\"$rowcls\" id=\"row$nrow\">"; //echo "<tr class=\"$rowcls\">\n";
				
				$ncol = 0;
				foreach($cols as $col) {
					//TODO: EL COL SPAN.
					$tdmod = '';
					if (isset($col['align'])) {
						$align = new bas_htm_tdalign($col['align']);
						$tdmod .= ' ' . $align->htmltext;
					} elseif (isset($col['dynamic_align'])) {
						eval("\$align = new bas_htm_tdalign(\"{$col['dynamic_align']}\");");
						$tdmod .= ' ' . $align->htmltext;							
					}
					
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
					  
					echo "<div class=\"tableCell\" id=\"col$ncol\"$tdmod>$data</div>";//echo "<td$tdmod>$data</td>\n";
					$ncol++;
				}
				echo "</div>\n"; //echo "</tr>\n";
				$rec=$this->dataset->next();
				$nrow++;
			}
				
			echo "</div></div>\n";//echo "</tbody>\n";
			
			if ($this->tabledef->hasfoot){
				echo "<div class=\"tableFooter\">\n"; //echo "<tfoot>\n";
				echo "\t<div class=\"tableRow\">"; //echo "<tr class=rowfoot>\n";
				$icol = 0;
				foreach($cols as $col) {
					$data = $col['foot'];
					if (isset($col['foottemplate'])) eval ("\$data=\"${col['foottemplate']}\";");
					echo "<div class=\"tableCell\" id=\"col$icol\">$data</div>"; //echo "<td$tdmod>$data</td>\n";
					$icol++;
				}
				echo "</div>\n"; //echo "</tr>\n";
				echo "</div>\n"; //echo "</tfoot>\n";
			} else {
				//TODO: CAMBIAR EL JAVASCRIPT PARA QUITAR ESTO.
				echo "<div class=\"tableFooter\">\n"; //echo "<tfoot>\n";
				echo "\t<div class=\"tableRow\">"; //echo "<tr class=rowfoot>\n";
				$icol = 0;
				foreach($cols as $col) {
					echo "<div class=\"tableCell\" id=\"col$icol\"></div>"; //echo "<td$tdmod>$data</td>\n";
					$icol++;
				}
				echo "</div>\n"; //echo "</tr>\n";
				echo "</div>\n"; //echo "</tfoot>\n";
			}
			
			
			echo "</div></div>\n";
			echo "<div class=\"vscroll\"><div class=\"vscrollsize\">V - V - V - V - V - V - V</div></div>\n";
			echo "<div class=\"hscroll\"><div class=\"hscrollsize\">H - H - H - H - H - H - H</div></div>\n";
			echo "</div>\n"; 
			// echo "</table>\n";
			
		}
	}
}
?>
