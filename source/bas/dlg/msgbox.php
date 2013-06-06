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
 * Mensaje al usuario.
 * @package dialog;
 */
class bas_dlg_msgbox{
	public $title;
	public $class;
	public $status;
	public $image;
	public $message;
	
	public function OnPaint(){
		$dlg = new bas_htm_dialog($this->title, $this->class, array('ok'), $this->image, $this->status);
		$dlg->p($this->message);
		$dlg->printme();
	}
	
	public function OnAction($action, $data){
		global $ICONFIG;
		switch ($action){
			case 'error':	$this->title = 'Error';
							$this->class = 'error';
							$this->image = isset($ICONFIG['ONPDA']) ? 'imgdialog_error.gif' : 'icon_dlgerror.png';
							$this->message = $data;
							break;
							
			case 'warning':	$this->title = 'Advertencia';
							$this->class = 'warning';
							$this->image = isset($ICONFIG['ONPDA']) ? 'imgdialog_warning.gif' : 'icon_dlgwarning.png';
							$this->message = $data;
							break;
							
			case 'info':	$this->title = 'Información';
							$this->class = 'info';
							$this->image = isset($ICONFIG['ONPDA']) ? 'imgdialog_.gif' : 'icon_dlginfo.png';
							$this->message = $data;
							break;
							
			default: return array('close');
		}
	}
	
}
?>
