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
class bas_htm_dialog extends bas_htm_page{
	
	public function __construct($title, $class='dialog', $actions='', $image='', $status=''){
		global $ICONFIG;
		
		parent::__construct($title);
		if (!$actions) $actions=array('ok');
		$this->style = isset($ICONFIG['ONPDA'])?'dialogpda' : 'dialog'; 
		if (!$status) $status='...';
		
		#level 0 - Encabezado 
		$this->openform();
		$this->opendiv('maindlg', $class);
		$this->opendiv('titlebar');	$this->h($title); $this->closediv();
		
		
		if (isset($ICONFIG['ONPDA'])) {
			$this->opendiv('contentbody');
			if ($image) $this->img($image);
			
			#level 2 - Pie
			$this->setlevel(2);
			$this->opendiv('dialogbuttons');
			$this->add(new bas_htm_buttonbar($actions));
			$this->closediv();
			$this->closediv(); #contentbody
			
		} else {
			$this->opendiv('contentbody');
			if ($image) $this->img($image,'lefticon');
			$this->opendiv('msgbox');
			
			#level 2 - Pie
			$this->setlevel(2);
			$this->add(new bas_htm_buttonbar($actions));
			$this->closediv(); #msgbox
			$this->closediv(); #contentbody
			$this->opendiv('statusbar'); $this->p($status); $this->closediv();
			
		}
		$this->closediv(); #maindlg
		$this->closeform();
		
		#level 1 - Contenido
		$this->setlevel(1);
	}
	
}
?>
