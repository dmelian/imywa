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
class bas_frmx_dialog extends bas_html_page{
	public $resizable= true;
	public $width= 400;
	public $modal= true;
	public $actions;
	
	public function OnLoad(){
		$this->jsClass= 'bas_frmx_dialog';
		$title= T(get_class($this));
		$this->title= $title ? $title : T(get_class().'_undefined');
		if (!isset($this->actions)) $this->actions= array('ok');
		$this->setTheme('blitzer');
		$this->addThemeStyle();
		$this->addScript('script/frmx/lib.js');
		$this->addScript('script/frmx/dialog.js');
		$this->addjqueryui();
	}
	
	public function OnPaintContent(){
		echo TD(get_class().'_undefined');
	}
	
	public function OnPaint(){
		$this->beginHtml();
		echo '<div class="ia_dialog">';
		$this->OnPaintContent();
		echo '</div>';
		$this->endHtml();				
	}
	
}
?>
