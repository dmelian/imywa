<?php
class bas_sys_lang{
	public $id= array();
	public $caption= array();
	public $description= array();
	
	public function getCaption($id){
		return isset($this->caption[$id])?$this->caption[$id] : "TEXT[$id]:undefined";
	}
	
	public function getDescription($id){
		return isset($this->description[$id])?$this->description[$id]:'';
	}
	
	public function load($fileId){
		global $CONFIG;
		global $ICONFIG;
		global $_APPLICATION;
		
		if (isset($_APPLICATION)) $ext=".{$_APPLICATION->language}.lng";
		else $ext=".en.lng";
		if(!isset($this->id[$fileId])){
			$this->id[$fileId]= 1;
			$file= explode('_',$fileId);
			switch($file[0]){
				case 'bas':
					array_shift($file);
					$this->loadFile("${CONFIG['SRCDIR']}".implode('/',$file).$ext, $fileId);
					break;
				case 'lib':
					array_shift($file);
					$this->loadFile("${CONFIG['SRCDIR']}libs/".implode('/',$file).$ext, $fileId);
					break;
						
				default:
					$this->loadFile("${ICONFIG['SRCDIR']}".implode('/',$file).$ext, $fileId);
			}
		}
	}
	
	public function loadFile($file, $fileId){
		global $_LOG;
		if (file_exists($file) ){
			if ($fileId=='lang') $fileId='';
			elseif (substr($fileId,-5)=='_lang') $fileId= substr($fileId, 0, -5);
//			if(isset($_LOG)) $_LOG->log("LANG: load($file,$fileId");
			$f= fopen($file,'r');
			$section= $fileId;
			while(!feof($f)){
				$textLine= trim(fgets($f));
				if ((strlen($textLine) > 0) && (substr($textLine,0,1) != '#')){
					if ((substr($textLine,0,1) == '[') && (substr($textLine,-1) == ']')) {
						$section= ($fileId ? "{$fileId}_":'').trim(substr($textLine,1,-1));
					} else {
						if ($caption= strpbrk($textLine," \t")){
							$id= trim(substr($textLine,0,strlen($textLine)-strlen($caption)));
							$caption= trim($caption);
							if (($sep= strpos($caption, '::')) !== false){
								$description= trim(substr($caption,$sep+2));
								$caption= trim(substr($caption,0,$sep));
							} else $description= '';
							$extId= ($section ? "{$section}_":'').$id;
							$this->caption[$extId]= $caption;
//							if(isset($_LOG)) $_LOG->log("LANG: caption[$extId]=$caption");
							if ($description) {
								$this->description[$extId]= $description;
							}
						}
					}
				}
			}
			fclose($f);
		}
	}
	
}

?>