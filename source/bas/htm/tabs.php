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
 * @package html
 */
class bas_htm_tabs extends bas_htm_elements{

	protected $tabs=array();
	private $id;
	private $selected = 0;
	private $level;
	
	public function __construct($id='main'){
		parent::__construct();
		$this->level = -1;
		$this->setlevel($this->level);
		$this->id = $id;
		$this->opendiv("tabcontent_$id", 'tabcontainer'); #contenedor de los cuerpos de los tabs
	}
	
	public function addtab($caption, $id=''){
		$this->level++;
		if ($this->level) {$this->closediv();}
		$this->setlevel($this->level);
		$id = "tabcontent$this->id$this->level";
		$this->tabs[] = array('caption'=>$caption, 'id'=>$id);
	}
	
	public function select($selected){$this->selected = $selected;}
	
	public function printme(){
		# los encabezados
		
		echo "<ul id=\"tab_$this->id\" class=\"tabs\">\n";
		$i = 0;
		foreach($this->tabs as $tab){
			echo ($i==$this->selected) ? "<li class=\"selected\">": "<li>";
			echo "<a href=\"#\" onClick=\"showtab('$this->id',$i);return false;\">";
			echo $tab['caption'];
			echo "</a></li>\n";
			$i++;
		}
		echo "</ul>\n";
		echo "<input type=\"hidden\" name=\"tabselected_$this->id\""
			. " id=\"tabselected_$this->id\" value=\"$this->selected\">\n";

		# agregamos los encabezados de las divisiones de contenido
		if ($this->level) {$this->closediv();} //Del ultimo tab
		$this->closediv(); //del content_container
		for ($i = 0; $i <= $this->level; $i++){
			$this->setlevel($i-1);
			$contentclass = $this->selected == $i ? 'tabcontentSelected' : 'tabcontent';
			$this->opendiv($this->tabs[$i]['id'], $contentclass);
		}
		
		# los contenidos
		parent::printme();
	
	}
	
	
}
?>
