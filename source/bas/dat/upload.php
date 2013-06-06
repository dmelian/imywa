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

	private function incchar($c){
		switch($c){
			case '9': return 'a';
			case 'z': return '0';
			default: return  utf8_encode(chr(ord($c)+1));
		}
	}
	
	private function incstring($s){
		if (strlen($s) == 0) return '1';
		$c = $this->incchar(substr($s,-1,1));
		if (!$rm = substr($s,0,-1)) $rm = '';
		if ($c == '0') return $this->incstring($rm).$c;
		else return "$rm$c";
	}

	private function newuploadkey($dstfolder){
		global $_LOG;
		if ($kfh = fopen("$dstfolder/.uploadkey",'c+')){
			$count = 0;
			while ((!$lock = flock($kfh,LOCK_EX)) && $count < 10){ usleep(50000); $count++; }
			if (!$lock) {
				$this->errormsg = "No ha podido bloquear el fichero de claves en la carpeta destino $dstfolder.";
				return false;
			}
			$fileValue= fread($kfh, 20);
			if (ord(substr($fileValue,-1,1)) === 10)	$fileValue = substr($fileValue,0,-1);
			$key = $this->incstring($fileValue);
			
			fseek($kfh,0); fwrite($kfh,$key);
			flock($kfh,LOCK_UN); fclose($kfh);
			
			
			
			return $key;
			
		} else {
			$this->errormsg = "No ha podido acceder al fichero de claves en la carpeta destino $dstfolder.";
			return false;
		}
	}	
	
	
	public function save($uploadid, $dstfolder = ''){
		global $_LOG;
		if (isset($_FILES[$uploadid])){
			if (!$dstfolder) $dstfolder = $this->dstfolder;
			else{
                if ($dstfolder[0] != '/'){
                    $relativepath = $dstfolder;
                    $dstfolder = $this->dstfolder;
                }
			}
			
			if (!$dstfolder) {
				$this->errormsg = "No se ha definido la carpeta destino.";
				return false;	
			}
			if (substr($dstfolder,-1,1) == '/') $dstfolder = substr($dstfolder,0,-1);

			$newkey = $this->newuploadkey($dstfolder);
			if (!$newkey) return false;
			
			$uploadpath = pathinfo(strtolower($_FILES[$uploadid]['name'])); 
			if (!isset($uploadpath['extension'])) $uploadpath['extension'] = '';
			$extension = strlen($uploadpath['extension']) == 0 ? "" : ".{$uploadpath['extension']}";
			
			if (isset($relativepath)) $dstfilename = "$dstfolder/$relativepath";
			else    $dstfilename = "$dstfolder";
			
			if (!file_exists($dstfilename)){
              $directory = explode( '/', $relativepath);
              $temp = $dstfolder;
              foreach($directory as $currentPath){
                  global $_LOG;
                  $_LOG->log("valor del path".$currentPath);
                  $temp = "$temp/$currentPath";
                  if (!file_exists($temp)){
                      mkdir($temp); chmod($temp, 0777);
                  }
              }                
            }	
            
            $dstfilename = "$dstfilename/u_$newkey$extension";
            
            
			$ret = move_uploaded_file($_FILES[$uploadid]['tmp_name'], $dstfilename);
			if ($ret) {
				$this->uploads[$uploadid] = pathinfo($dstfilename);
				$this->uploads[$uploadid]['originalname'] = $uploadpath['basename'];
				$this->uploads[$uploadid]['uploadkey'] = $newkey; 
				
			} else $this->errormsg = "No se ha podido copiar el fichero '{$_FILES[$uploadid]['tmp_name']}' a su destino $dstfilename";
			return 	$ret;
			
		} else {
			$this->errormsg = "No se encuentra el upload $uploadid"; 
			return false;
		}
	}

	private function getuploadfilename($dstfolder, $uploadkey){
		$dir = opendir($dstfolder);
		$found = false; 
		while (!$found && ($filename = readdir($dir)) ) {
			$extpos = strrpos($filename, '.');
			if ($extpos === false) $extpos = strlen($filename);
			$filekey = substr($filename, 2, $extpos -2); 
			$found = $filekey == $uploadkey;
		}
		closedir($dir);
		return $found ? $filename : false;
	}
	
	public function replace($uploadkey, $uploadid, $dstfolder=''){
		#uploadkey - es la clave que identifica el upload
		#uploadid - es el identificador del upload html.
		
		if (isset($_FILES[$uploadid])){
			if (!$dstfolder) $dstfolder = $this->dstfolder;
			if (!$dstfolder) {
				$this->errormsg = "No se ha definido la carpeta destino.";
				return false;	
			}
			if (substr($dstfolder,-1,1) == '/') $dstfolder = substr($dstfolder,0,-1);
			
			$oldfilename = $this->getuploadfilename($dstfolder, $uploadkey);

			$uploadpath = pathinfo(strtolower($_FILES[$uploadid]['name'])); 
			if (!isset($uploadpath['extension'])) $uploadpath['extension'] = '';
			$extension = strlen($uploadpath['extension']) == 0 ? "" : ".{$uploadpath['extension']}";
			$dstfilename = "$dstfolder/u_$uploadkey$extension";
			
			if ($renamed = "$dstfolder/$oldfilename" == $dstfilename) {
				rename("$dstfolder/$oldfilename","$dstfolder/tmp_$oldfilename");
				$oldfilename = "tmp_$oldfilename";		
			}
			
			$ret = move_uploaded_file($_FILES[$uploadid]['tmp_name'], $dstfilename);
			if ($ret) {
				$this->uploads[$uploadid] = pathinfo($dstfilename);
				$this->uploads[$uploadid]['originalname'] = $uploadpath['basename'];
				$this->uploads[$uploadid]['uploadkey'] = $uploadkey; 
				unlink("$dstfolder/$oldfilename");			
			} else { 
				$this->errormsg = "No se ha podido copiar el fichero a su destino $dstfilename";
				if ($renamed) rename("$dstfolder/$oldfilename", "$dstfolder/".substr($oldfilename, 4));
			}
			return 	$ret;
			
		} else {
			$this->errormsg = "No se encuentra el upload $uploadid"; 
			return false;
		}
	}

	public function uploaded($uploadid){
		return ($_FILES[$uploadid]['name'] != '');
	}
	
	public function getwebfilename(&$filename){
		global $ICONFIG;
		$folderseparator = strpos($filename,':');
		if ($folderseparator !== false){
			$folderid = substr($filename,0,$folderseparator);
			if (substr($folderid,0,4) == 'HOST') $folderid = 'HTTP'.substr($folderid, 4);
			$filename = substr($filename, $folderseparator+1);
			return $ICONFIG[$folderid] . $filename;
		} else return $filename;
	}
	
	public function getuploadinfo($uploadid){
		$qry = new bas_sql_myquery("select id, idcarpetadestino, extension, observaciones"
			. ", concat(idcarpetadestino,':','u_',id,if(not extension is null, concat('.',extension),'')) as filename"
			. " from upload where id ='$uploadid'");
		if ($qry->success){
			$uploadinfo = $qry->result;
			$uploadinfo['webfilename'] = $this->getwebfilename($uploadinfo['filename']);
		} else $uploadinfo = array();
		return $uploadinfo;
	}
	
}
