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
class bas_htm_form extends bas_htm_page{
	
	public function __construct($title, $toolbar=array()){
		global $ICONFIG;
		
		
		parent::__construct($title);
		//TODO: Añadir las acciones por defecto (cerrar almenos).
		
		if (isset($ICONFIG['ONPDA'])){
			$this->style = 'formpda'; 
			
			#level 0 - Encabezado 
			$this->openform();
			
			#level 2 - Pie
			$this->setlevel(2);
			$this->closeform();
			
			#level 1 - Contenido
			$this->setlevel(1);
			
		} else {
		
			$this->style = 'form'; 
			# 0 - Titulo
			# 1 - Form Menu
			# 2 - Barra de heramientas

			# 10 - Contenido
			
			# 20 - Pie
			
			
			#level 0 - Encabezado 
			$this->openform();
			$this->opendiv('', 'head');
			$this->opendiv('', 'title');$this->h($title); $this->closediv();
#			$this->opendiv('', 'formmenu');

			$this->setlevel(2);
#			$this->closediv(); #formmenu;
			$this->opendiv('', 'toolbar'); 
			$this->add(new bas_htm_buttonbar($toolbar));
			$this->closediv();
			$this->closediv();
			$this->opendiv('', 'body');
			
			#level 2 - Pie
			$this->setlevel(20);
			$this->closediv(); #body
			$this->opendiv('', 'foot'); $this->closediv();
			$this->closeform();
			
			#level 1 - Contenido
			$this->setlevel(10);
		}
	}
	
	public function setformmenu($formmenu){
		$curlevel = $this->curlevel;
		$this->setlevel(1);
		
		$this->add($formmenu);
		$this->addevent('onLoad','adjustmenus()');
		
		$this->setlevel($curlevel); 
	}
	
}
?>
