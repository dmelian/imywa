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

class bas_frmx_frame{
	public $id;
	public $title;
	public $jsClass= 'bas_frmx_frame';
	public $permission;
	public $actions=array();
	public $paintWrapper= true;
	
	public function __construct($id,$title=""){ 
		$this->id= $id;
		if ($title == "")	$this->title= $id; 
		else $this->title= $title;
	}
	
	public function OnLoad($permission= array('permission'=>'allow')){
		$this->permission= $permission;
		$this->checkActionsPermissions();
	}
	
	public function checkActionsPermissions(){
		switch ($this->permission['permission']){
			case 'except':
			case 'only':			
		}
	}
	
	public function OnPaint($page){
		$page->addDiv('ia_frame', $this->id);
		$page->addDiv('ia_frame_header'); echo $this->title;
		$page->nextDiv('ia_frame_content');
		$this->OnPaintContent($page);
		$page->closeDiv('ia_frame');
	}
}

?>
