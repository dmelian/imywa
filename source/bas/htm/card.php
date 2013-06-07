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
 * Ficha Html.
 * @package html
 */
class bas_htm_card{
	private $cols;
	private $record;
	
	public function __construct(&$cols, $record=array()){ 
		$this->cols = $cols;
		$this->record = $record;
	}
	
	public function printme(){
		global $_LOG;
		$template = new bas_htm_template();

		
		echo "<table>\n";
		echo "<tbody>\n";
		$cols = is_object($this->cols) ? $this->cols->getcols(): $this->cols;		
		foreach($cols as $col) {
			echo "<tr>\n";
			echo "<td class=\"card_caption\">${col['caption']}</td>\n";
			$data = isset($this->record[$col['id']]) ? $this->record[$col['id']]:'';
			if (is_object($this->cols) && isset($col['template'])) {
				$data = $template->stamp($this->cols, $col['id'], $this->record);
//				$data = $col['template']->stamp($data, $this->record);
			} elseif (isset($col['format'])) {
				$format = new bas_dat_format($col['format']);
				$data = $format->format($data);
			}
			echo "<td class=\"card_content\">$data</td>\n";
			echo "</tr>\n";
		}	
		echo "</tbody>\n";
		
		echo "</table>\n";
		
	}
}

?>
