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

class bas_dat_paragraphs{
	public $paragraphs= array();
	private $defproperties= array();
	
	public function __constructor($default=array()){
		$this->defproperties= isset($defaul['align']) ? $default['align']: "left";
	}
	
	public function setDefault($property, $value){
		
	}
	
	public function insertText($text, $align=''){
		if (!$align) $align="left"; //Posibles values: "right", "left", "center", "justify"
		$this->paragraph[]=array(
			"type"=>'text',
			"align"=>$align,
		);
	}
	
	public function insertMarkdown(){
		
	}
	
	public function insertHtml(){
		
	}
	
	public function insertHeader(){
		
	}
	
	public function insert_image(){
		
	}
	
}
?>