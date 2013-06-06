<?php
class bas_sysx_lang{
	private $id= array();
	private $caption= array();
	private $description= array();
	
	private $lastId;
	private $lastTexts;
	
	public function getCaption($id, $texts=false){
		$this->lastId= $id;	$this->lastTexts= $texts;
		$caption= isset($this->caption[$id])?addslashes($this->caption[$id]) : "TEXT[$id]:undefined";
		if ($texts !== false){
			extract($texts, EXTR_PREFIX_ALL,'');
			eval ("\$caption = \"$caption\";");
		}
		return $caption;
	}
	
	public function getDescription($id='', $texts=false){
		global $_LOG;
		if (!$id) {$id= $this->lastId; $texts= $this->lastTexts;} 
		$description= isset($this->description[$id])?addslashes($this->description[$id]):'';
		if ($texts !== false){
			extract($texts, EXTR_PREFIX_ALL,'');
			eval ("\$description = \"$description\";");
		}
		return $description;
	}
	
	public function load($id, $file=''){
		global $_SESSION;
		global $CONFIG;
		
		if (!$file) $file= getclassfile($id);
		if (isset($_SESSION)) $ext="{$_SESSION->language}.lng";
		else $ext="{$CONFIG['DEFAULTLANGUAGE']}.lng";
		
		if(!isset($this->id[$id])) {
			$this->loadFile($id, "$file.$ext");
			$this->id[$id]= 1;
		}
	}
	
	private function loadFile($id, $file){
		
		if (file_exists($file)){
			$f= fopen($file,'r');
			$section= $id;
			while(!feof($f)){
				$textLine= trim(fgets($f));
				if ((strlen($textLine) > 0) && (substr($textLine,0,1) != '#')){
					if ((substr($textLine,0,1) == '[') && (substr($textLine,-1) == ']')) {
						$section= ($id ? "{$id}_":'').trim(substr($textLine,1,-1));
					} else {
						if ($caption= strpbrk($textLine," \t")){
							$text_id= trim(substr($textLine,0,strlen($textLine)-strlen($caption)));
							$caption= trim($caption);
							if (($sep= strpos($caption, '::')) !== false){
								$description= trim(substr($caption,$sep+2));
								$caption= trim(substr($caption,0,$sep));
							} else $description= '';
							$ext_text_id= ($section ? "{$section}_":'').$text_id;
							$this->caption[$ext_text_id]= $caption;
							if ($description) {
								$this->description[$ext_text_id]= $description;
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