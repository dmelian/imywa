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
class bas_html_page{

	public $styles= array();
	public $scripts= array();
	public $title;
	public $jsClass;
	public $theme;

	private $_openDivs= array();

	public function __construct(){
		global $_SESSION;
		if (isset($_SESSION) && $_SESSION->theme) $this->setTheme($_SESSION->theme);
		$this->jsClass= get_class($this);
	}
	
// Themes	
	
	public function setTheme($theme){ $this->theme=$theme; }

// Styles	
	
	public function addStyle($style, $absolute=false){
		if (!$absolute){
			if (isset($this->theme)) $style= "theme/{$this->theme}/$style";
			else $style= "style/$style";
		} 
		$this->styles[]=$style; 
	}
	public function addThemeStyle(){ $this->styles[]= "theme/{$this->theme}/{$this->theme}.css \" id=\"theme_selected_css";}

// Scripts	
	
	public function addScript($script){ $this->scripts[]=$script; }
	
	public function myscript(){
		if (function_exists('getclassfile')){
			$jsClassFile= getclassfile(get_class($this)). '.js';
			if (file_exists($jsClassFile)){
				include $jsClassFile;
			}
		}
	}
	
// JQuery

	public function addjqueryui($url='local'){
		/*
		if (isset($this->theme)) $this->addStyle("jqueryui.css");
		else $this->addStyle("jquery/jqueryui.css");
		*/
		//if (!isset($this->theme)) $this->addStyle("jquery/jqueryui.css");
		switch($url){
		case 'local':
			$this->addScript('script/jquery/jquery-1.8.3.js');
			$this->addScript('script/jquery/jquery-ui-1.9.2.custom.js');
			
// 			$this->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
// 			$this->addScript('http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js');
			
			break;
		default:
			$this->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js');
			$this->addScript('http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js');
			$this->addScript('http://ajax.googleapis.com/ajax/libs/jqueryui/1.3.2/jquery-ui.min.js');	
		}
	}

// Forms
	public function openForm($id=''){
		$id = $id ? " id=\"$id\"" : '';
		echo "<form$id method=	\"post\" enctype=\"multipart/form-data\">";
		echo getsessionstamp();
	}
	
	public function closeForm(){
		echo '</form>';
	}
	

// Divs
	
	public function addDiv($div, $id='', $style=""){
		array_push($this->_openDivs, $div);
		$id= $id ? " id=\"$id\"" : '';
		$style= $style ? " style=\"$style\"" : '';
		echo "<div class=\"$div\"$id $style>";
	}
	
	public function nextDiv($div, $id=''){
		array_pop($this->_openDivs); echo '</div>';
		$this->addDiv($div, $id);
	}
	
	public function closeDiv($div=false){
		while (array_pop($this->_openDivs) != $div) echo '</div>';
		echo '</div>';
	}
	
	public function closeAllOpenDivs(){
		for ($idiv=0; $idiv < count($this->_openDivs); $idiv++) echo '</div>';
		unset($this->_openDivs); $this->_openDivs= array();
	}
	
	
// Paint HTML	
	public function beginHtml(){
		global $_SESSION;
		echo '<!doctype html>';
		$language= isset($_SESSION) ? $_SESSION->language: 'es';
		echo "<html lang=\"$language\">";
		echo '<head>';
		echo '<meta charset="utf-8" />';
		echo '<meta  name="viewport"  content="width=device-width, initial-scale=1.0">';
		echo '<link rel="icon" href="image/imywa.png" type="image/png">';
		echo "<title>$this->title</title>";
		foreach($this->styles as $style) echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$style\">";
		foreach($this->scripts as $script) echo "<script type=\"text/javascript\" src=\"$script\"></script>";
		
		if ($this->jsClass){
			echo "<script>\n\$(function(){\n";
			echo "currentForm= new {$this->jsClass}();\n";
			echo "bas_copyAttributes(JSON.parse(\"" . addcslashes(json_encode($this),'"\\/') . "\"), currentForm);\n";
			echo "currentForm.OnLoad();\n";
			echo "});\n</script>";
		}	
		if (method_exists($this, 'myscript')) {
			echo '<script id="myscript">';
			$this->myscript();
			echo '</script>';
		}
		
		echo '</head><body>';
	}

	public function endHtml(){
		$this->closeAllOpenDivs();
		echo '</body></html>';
	}
}
?>
