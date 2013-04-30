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
class bas_htm_formlist extends bas_htm_page{
	private $menu;
	
	public function __construct($title, $menu, $toolbar=array()){
		
		
		parent::__construct($title,'form2','form2');
		$this->addevent('onload', 'loadform()');
		$this->menu = $menu; 			
		
		#level 0 - Encabezado 
		$this->openform();
		$this->opendiv('titlebar');$this->h($title); $this->closediv();
		$this->opendiv('menubar'); $this->addme('menubar'); $this->closediv();
		$this->opendiv('menus'); $this->addme('menus'); $this->closediv();
		$this->opendiv('toolbar'); $this->add(new bas_htm_buttonbar($toolbar));	$this->closediv();
		$this->opendiv('content');
		
		#level 2 - Pie
		$this->setlevel(2);
		$this->closediv(); #content
		$this->opendiv('statusbar'); $this->p('STATUS BAR'); $this->closediv();
		$this->closeform();
		
		#level 1 - Contenido
		$this->setlevel(1);
		
	}
	
	protected function printmyelement($element){
		switch ($element){
			case 'menubar':	$this->menu->printdivmenubar(); break;
			case 'menus': $this->menu->printdivmenus(); break;
		}
	}	
	
}
?>
