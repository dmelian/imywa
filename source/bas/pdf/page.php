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
class bas_pdf_page {
	protected $elements;
	private $curlevel;
	private $fpdf;
	
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
	}
	
	public function closeform(){
	}
	
	public function opendiv($id='', $class=''){
	}
	
	public function closediv(){
	}
	
	
	public function pdfme(){
		$this->fpdf=new lib_fpdf_fpdf('P','mm','A4');
		$this->fpdf->SetMargins(20,20);
		$this->fpdf->AddPage();
		$this->fpdf->SetFont('Helvetica', '', 10);
		$this->fpdf->SetAutoPageBreak(true, 20);
		
		$this->fpdf->currenttest();
		
		ksort($this->elements);
		foreach($this->elements as $levelelements) $this->pdfelements($levelelements);
		
		$this->fpdf->output();
	}
	
	protected function pdfelements($elements){
		
		foreach ($elements as $elemententry){
			$element = isset($elemententry['value'])?$elemententry['value']:'';
			switch($elemententry['type']){
				case 'X': 	$this->fpdf->out($element); 
							break;
							
				case 'P': 	$this->fpdf->write(8, $element);	 
							break;
							
				case 'H': 	$this->fpdf->write(12, $element); 
							break;
							
				case 'I':	$this->fpdf->Image($element, 20, 20, 100, 150);
						
				default: 	$element->pdfme($this->fpdf);
			}
		}
	}
	
}
?>
