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
class bas_dat_upload{
	private $dstfolder;
	public $errormsg;
	public $uploads = array();
	
	public function __construct($dstfolder='./uploads'){
		$this->dstfolder = $dstfolder;
	}
	
/*
	public function savefromtabledef($tbdef, $id=''){
		$ok = true;
		foreach($_FILES as $ifile => $file) if(($ifile == $id) || !$id){
			$col = $tbdef->getcol($ifile);
			$dstfolder = isset($col['template_properties']['dstfolder'])?$col['template_properties']['dstfolder']:$this->dstfolder; 
			$ok = $ok && $this->save($ifile, $dstfolder);
		}
		return $ok;
	}
*/	
	public function save($id, $filename='', $dstfolder = ''){
		
		if (isset($_FILES[$id])){
			if (!$dstfolder) $dstfolder = $this->dstfolder;
			if (!$filename) $filename = strtolower($_FILES[$id]['name']);
			$dstfilename = $dstfolder . $filename;
			if (!file_exists($dstfilename))	{
				$uploadpath = pathinfo(strtolower($_FILES[$id]['name'])); if (!isset($uploadpath['extension'])) $uploadpath['extension'] = '';
				$filepath = pathinfo($filename); if (!isset($filepath['extension'])) $filepath['extension'] = '';
				if ($uploadpath['extension'] && !$filepath['extension']) $dstfilename = "$dstfolder{$filepath['filename']}.{$uploadpath['extension']}"; 
				$ret = move_uploaded_file($_FILES[$id]['tmp_name'], $dstfilename);
				if ($ret) {
					$this->uploads[$id] = pathinfo($dstfilename);
					$this->uploads[$id]['originalname'] = $uploadpath['basename'];	
				} else $this->errormsg = "No se ha podido copiar el fichero $filename a su destino";
				return 	$ret;
			}
			else {
				$this->errormsg = "El fichero $filename ya existe."; 
				return false;
			}			
		} else {
			$this->errormsg = "No se encuentra el upload $id"; 
			return false;
		}
	}

	
	public function uploaded($id){
		return ($_FILES[$id]['name'] != '');
	}
	
	
	public function replace($id, $filename='', $dstfolder=''){
		
		if (isset($_FILES[$id])){
			if (!$dstfolder) $dstfolder = $this->dstfolder;
			if (!$filename) $filename = strtolower($_FILES[$id]['name']);
			$dstfilename = $dstfolder . $filename;
			if (file_exists($dstfilename)) unlink($dstfilename);			
			$uploadpath = pathinfo(strtolower($_FILES[$id]['name'])); if (!isset($uploadpath['extension'])) $uploadpath['extension'] = '';
			$filepath = pathinfo($filename); if (!isset($filepath['extension'])) $filepath['extension'] = '';
			if ($uploadpath['extension'] != $filepath['extension']) $dstfilename = "$dstfolder{$filepath['filename']}.{$uploadpath['extension']}"; 
			$ret = move_uploaded_file($_FILES[$id]['tmp_name'], $dstfilename);
			if ($ret) {
				$this->uploads[$id] = pathinfo($dstfilename);
				$this->uploads[$id]['originalname'] = $uploadpath['basename'];	
			} else $this->errormsg = "No se ha podido copiar el fichero $filename a su destino";
			return 	$ret;
		} else {
			$this->errormsg = "No se encuentra el upload $id"; 
			return false;
		}
	}
	
	
}
