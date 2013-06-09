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
	public $formClassName;
	
	
	public function __construct($id,$title=""){
		$this->id= $id;
		if ($title == "")	$this->title= $id; 
		else $this->title= $title;
	}
	
	public function setFormClassName($className){ $this->formClassName= $className; }
	
	public function saveConfigFile($config, $id=''){
		if (!$this->formClassName) return;
		$f= fopen($this->defaultConfigFilename($id, true),'w');
		foreach($config as $key => $val){
			if(is_array($val)) {
				fwrite($f,"\n[$key]\n");
				foreach($val as $skey => $sval){
					if (!is_numeric($sval)) $sval= "\"$sval\""; 
					fwrite($f, "$skey = $sval\n");
				}
			} else {
				if (!is_numeric($val)) $val= "\"$val\""; 
				fwrite($f, "$key = $val\n");
			}
		}
		fclose($f);
	}

	public function loadConfigFile($id=''){
		if (!$this->formClassName) return array();
		$filename= $this->defaultConfigFilename($id);
		return parse_ini_file($filename, true);
	}
	
	public function defaultConfigFilename($id, $createFolders=false){
		global $_SESSION; global $CONFIG;
		$class= explode('_', $this->formClassName);
		if ($id) $id= "_$id";
		$userid= $_SESSION->user ? $_SESSION->user : 'default';
		$filename= "{$CONFIG['BASDIR']}config/$userid/".implode('/',$class)."_{$this->id}$id";
		if ($createFolders){
			$filePath= substr($filename, 0 , strrpos($filename, '/'));
			if (!file_exists($filePath)) mkdir($filePath, 0777, true);
		}
		return $filename;
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
