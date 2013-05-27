<?php

// SYSTEM CONFIGURATION
$CONFIG['BASDIR']= '/usr/local/imywa/';
$CONFIG['WWWDIR']= '/var/www/apps/';
$CONFIG['DEFAULTLANGUAGE']= 'es';
$CONFIG['DEFAULTTHEME']= 'amedita';
$CONFIG['DEFAULTHOST']= 'localhost';
$CONFIG['DEFAULTDATABASE']= 'imywa'; 

// CHECKS
$CONFIG['TERMINAL'] = isset($_GET['terminal']) ? $_GET['terminal'] : 'UNKNOWN';
$CONFIG['BROWSER'] = preg_match('/MSIE/i', $_SERVER['HTTP_USER_AGENT']) ? 'IE' : 'COMPATIBLE';


// GLOBAL FUNCTIONS
function getclassfile($class_name){
	global $CONFIG;
	
	$class= explode('_',$class_name); 
	return "${CONFIG['BASDIR']}source/".implode('/',$class);
}

function __autoload($class_name) {
	global $_LANG;
	
	$class_module= ($last_= strrpos($class_name,'_')) > 0 ? substr($class_name, 0, $last_): '';
	if (isset($_LANG)) $_LANG->load($class_module, getclassfile($class_module).'/lang'); 
	require_once getclassfile($class_name).'.php';
}


function getsessionstamp(){
	global $_APPLICATION;
	
	if(is_object($_APPLICATION) && $_APPLICATION->session->sessionno) {
		$ret = "<input type=\"hidden\" name=\"sessionno\" value=\"".$_APPLICATION->session->sessionno."\">";
		if($_APPLICATION->installationid) $ret .= "<input type=\"hidden\" name=\"installationid\" value=\"$_APPLICATION->installationid\">";
		if($_APPLICATION->nextsequenceno) $ret .= "<input type=\"hidden\" name=\"sequenceno\" value=\"$_APPLICATION->nextsequenceno\">";
		return $ret;
	}
}

// function getBreadCrumbStamp(&$form= false){
// 	global $_SESSION;
// 	if (isset($_SESSION->currentApp)) {
// 		$captions= $_SESSION->apps[$_SESSION->currentApp]->getBreadCrumbCaptions();
// 		if ($form && method_exists($form,'getBreadCrumbCaption')){
// 			array_push($captions, $form->getBreadCrumbCaption());
// 		} 
// 	
// 		$jump= count($captions);
// 		$ret='<ul class="breadcrumb">';
// 		foreach($captions as $caption){
// 			$jump--;
// 			$ret.="<li onclick=\"ajaxaction('close', {'jump':$jump});\"><span>$caption</span></li>";// <span> > </span></li>";
// 		}
// 		$ret.='</ul>';
// 	} else $ret= '';
// 	return $ret;
// }

function getBreadCrumbStamp(&$form= false){
	global $_SESSION;
	if (isset($_SESSION->currentApp)) {
		$captions= $_SESSION->apps[$_SESSION->currentApp]->getBreadCrumbCaptions();
		if ($form && method_exists($form,'getBreadCrumbCaption')){
			array_push($captions, $form->getBreadCrumbCaption());
		} 
		$aux = array_reverse($captions);
		$captions = $aux;
		$jump= 0;
		$ret='<ul class="crumbs">';
		$hover="ia_bread_fixed";
		$breaditems = "";
		global $_LOG;
		$_LOG->debug("valor del captions",$captions);
		if (count($captions)== 1){
			$breaditems ="<li>"."<a class=\"\" style=\"z-index: $jump;\" > <div> ". $captions[0] ." </div> </a> </li>";// <span> > </span></li>";
		}
		else{
			foreach($captions as $caption){			
				$bread ="<li>"."<a class=\"\" style=\"z-index: $jump;\" onclick=\"ajaxaction('close', {'jump':$jump});\" > "."<div>  $caption  </div> </a> </li>";// <span> > </span></li>";
				$breaditems = $bread . $breaditems;
	// 			"<a class=\"ia_bread_item $hover\" style=\"z-index: $jump;\" onclick=\"ajaxaction('close', {'jump':$jump});\" > <div> <span> $caption </span> </div> </a> </li>";// <span> > </span></li>";
				if($hover=="ia_bread_fixed")$hover="ia_bread_hover";
				$jump++;
			}
		}
		$ret.= $breaditems . '</ul>';
	} else $ret= '';
	return $ret;
}

function T($id1, $id2='', $txts=false){
	global $_LANG;
	if (is_object($id1)) $id1= get_class($id1);
	if ($id2) $id2="_$id2";
	return $_LANG->getCaption("$id1$id2", $txts);
}

function TD($id1='', $id2='', $txts=false){
	global $_LANG;
	if (is_object($id1)) $id1= get_class($id1);
	if ($id2) $id2="_$id2";
	return $_LANG->getDescription("$id1$id2", $txts);
}

class syncFile {

	public $errormsg;
	public $content;
	public $filename;
	
	public function __construct($filename){ $this->filename= $filename; }
	
	public function getContent(){
		
		if ($file= fopen($this->filename,'r')){
			$count = 0;
			while ((!$lock = flock($file,LOCK_EX)) && $count < 10){ usleep(50000); $count++; }
			if (!$lock) { 
				$this->errormsg = "No ha podido bloquear el fichero {$this->filename}.";
				return false;
			}
			fseek($file,0); $this->content= fread($file, filesize($this->filename));
			flock($file,LOCK_UN); fclose($file);
			return true;
			
		} else {
			$this->errormsg = "No ha podido acceder el fichero {$this->filename}.";
			return false;
		}
	}
	
	function setContent($content){
		
		$newfile= !file_exists($this->filename);
		if ($file= fopen($this->filename,'c+')){
			$count = 0;
			while ((!$lock = flock($file,LOCK_EX)) && $count < 10){ usleep(50000); $count++; }
			if (!$lock) { 
				$this->errormsg = "No ha podido bloquear el fichero {$this->filename}.";
				return false;
			}
			fseek($file,0); fwrite($file, $content);
			flock($file,LOCK_UN); fclose($file);
			if ($newfile) chmod($this->filename, 0666);
			return true;
			
		} else {
			$this->errormsg = "No ha podido acceder el fichero {$this->filename}.";
			return false;
		}
		
	}
			
}

?>
