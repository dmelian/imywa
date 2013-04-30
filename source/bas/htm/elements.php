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
/**
 * 
 * @package html
 */
class bas_htm_elements {
	protected $elements;
	protected $openform;
	protected $opendivs;
	protected $curlevel;
	
	//TODO: El tratamiento del precontent y el postcontent se podrían tratar como variables $preelements y postelements. Ahí queda
	
	public function __construct(){
		$this->elements = array();
		$this->setlevel(0);
	}
	
	public function setlevel($level){
		$this->curlevel = $level;
		if (!isset($this->elements[$this->curlevel])) $this->elements[$this->curlevel]=array();
	}
	
	public function add($element){
		$this->elements[$this->curlevel][] = array('type'=>'E', 'value'=>$element);
	}
	
	protected function addme($element){
		$this->elements[$this->curlevel][] = array('type'=>'ME', 'value'=>$element);
	}
	
	public function direct($html){
		$this->elements[$this->curlevel][] = array('type'=>'X', 'value'=>$html);
	}
	
	public function p($text, $class=''){
		$this->elements[$this->curlevel][] = array('type'=>'P', 'value'=>$text, 'class'=>$class);		
	}
	
	public function h($text, $level=1){
		$this->elements[$this->curlevel][] = array('type'=>'H', 'level'=>$level, 'value'=>$text);		
	}
	
	public function img($image, $class=''){
		$this->elements[$this->curlevel][] = array('type'=>'I', 'class'=>$class, 'value'=>$image);		
	}
	
	public function openform(){
		$this->elements[$this->curlevel][] = array('type'=>'F');
	}
	
	public function closeform(){
		$this->elements[$this->curlevel][] = array('type'=>'FC');
	}
	
	public function opendiv($id='', $class=''){
		$this->elements[$this->curlevel][] = array('type'=>'D', 'id'=>$id, 'class'=>$class);
	}
	
	public function closediv(){
		$this->elements[$this->curlevel][] = array('type'=>'DC');
	}
	
	public function printme(){
		$this->openform = false;
		$this->opendivs = 0;
		ksort($this->elements);
		foreach($this->elements as $levelelements){
			$this->printelements($levelelements);
		}
		$this->printclose();
	}
	
	protected function printelements($elements){
		
		foreach ($elements as $elemententry){
			$element = isset($elemententry['value'])?$elemententry['value']:'';
			switch($elemententry['type']){
				case 'X': 	echo $element."\n"; 
							break;
							
				case 'P': 	$class = $elemententry['class'] ? " class=\"{$elemententry['class']}\"" : ''; 
							echo "<p$class>$element</p>\n"; 
							break;
							
				case 'H': 	echo "<h${elemententry['level']}>$element</h${elemententry['level']}>\n"; 
							break;
							
				case 'I': 	echo "<img";
							if ($elemententry['class']) echo " class=\"${elemententry['class']}\""; 
							echo " src=\"image/$element\">\n"; 
							break;
							
				case 'F': 	$this->openform=true; 
							echo "<form method=\"post\" enctype=\"multipart/form-data\">\n";
							echo getsessionstamp()."\n";
							break;
							
				case 'FC': 	$this->openform=false; 
							echo "</form>\n"; 
							break;
							
				case 'D':	$this->opendivs++;
							echo "<div";
							if ($elemententry['class']) echo " class=\"${elemententry['class']}\"";
							if ($elemententry['id']) echo " id=\"${elemententry['id']}\"";
							echo ">\n";
							break;
							
				case 'DC':	$this->opendivs--;
							echo "</div>\n";
							break;
						
				case 'ME':	$this->printmyelement($elemententry['value']);
							break;

				default: $element->printme();
			}
		}
	}
	
	protected function printmyelement($element){
		
	}
	
	protected function printclose(){
		if ($this->openform) {
			$this->openform=false;
			echo "</form>\n";
		}
		while ($this->opendivs>0) {
			$this->opendivs--;
			echo "</div>\n";
		}
	}

}
?>
