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

class bas_sys_frmconfig {

	private function getcfgfilename($classname, $createdirs = false){
		global $CONFIG;
		global $_APPLICATION;
		
		$filename = "{$CONFIG['RUNDIR']}{$_APPLICATION->installationid}"; 
		if ($createdirs && !file_exists($filename)){ mkdir($filename); chmod($filename, 0777);}
		$filename .= "/frmconfigs";
		if ($createdirs && !file_exists($filename)){ mkdir($filename); chmod($filename, 0777);}
		$filename .= "/{$_APPLICATION->user}";
		if ($createdirs && !file_exists($filename)){ mkdir($filename); chmod($filename, 0777);}
		return "$filename/$classname.cfg";
	}
	
	public function save($classname, $data){
		$filename = $this->getcfgfilename($classname, true);
		if ($fp = fopen($filename,"w")) {
			fwrite($fp, serialize($data));
			fclose($fp);
			chmod($filename, 0666);
		}
		
	}
	
	public function load($classname){
		$filename = $this->getcfgfilename($classname);
		return file_exists($filename) ? unserialize(file_get_contents($filename)) : false;
	}
	
	public function saveAsObject($classname, $jsonString){
		$this->save($classname, json_decode($jsonString));
	}
	
	public function loadAsString($classname){ 
		if ($ret= $this->load($classname)) return htmlentities(json_encode($ret)); 
		else return false;  	
	}
	
}

?>
