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

class bas_pdf_list{
	/* Crea una tabla en formato especifico para el nuevo formulario de lista */
	
	private $tabledef;
	private $dataset;
	private $extension;
	
	public function __construct(&$tabledef, &$dataset){
		$this->tabledef =& $tabledef;
		$this->dataset =& $dataset;
		$this->extension = array('*');
	}
	
	public function preextend(&$extension){array_unshift($this->extension, &$extension);}
	public function postextend(&$extension){array_push($this->extension, &$extension);}
	
	//compara dos filas iguales y devuelve los valores maximos en una tercera.
	public function rowMax($f1,$f2){
		$nf1 = count($f1);
		for($i=0;$i<$nf1;$i++){
			if($f1[$i] >= $f2[$i]){
				$fMax[$i] = $f1[$i];
			}
			else{
				$fMax[$i] = $f2[$i];
			}
		}
		return $fMax;
	}

	public function cabecera($ini,$fin,&$fpdf,$cols){
		if ($this->tabledef->hashead){
			$icol = 0;
			$content = array();
			foreach($cols as $col) {
				$data = $col['caption'];
				if($icol >= $ini && $icol <= $fin){
					
					if (isset($col['headtemplate'])) eval ("\$data=\"${col['headtemplate']}\";");

					$content[$icol] = $data;
				}
				
				$icol++;
			}
			$fpdf->tableBegin($content);
			
		}

	}


	public function footer(&$fpdf){
	    // Go to 1.5 cm from bottom
	    $fpdf->SetY(-15);
	    // Select Arial italic 8
	    $fpdf->SetFont('Helvetica','I',8);
	    // Print current and total page numbers
	    $fpdf->Cell(0,10,'Página '.$fpdf->PageNo().'/{nb}',0,0,'C');
	   
	}
	

	public function cuerpo($ini,$fin,&$fpdf,$cols,$inic,$finc){
		global $_LOG;
		$_LOG->log("############# Entramos en el cuerpo de PDF_LISTFRAME");
		$rec = $this->dataset->reset();
		if (method_exists($this->tabledef, 'getrecordkey'))	$currkey = $this->tabledef->getrecordkey();
		else $currkey = false;
			

		$nrow = 0;
		while ($rec){
			foreach($this->extension as $extension){
				if ($extension != '*') $extension->mergedata($rec);
			}
			
				$content = array(); 
				$ncol = 0;
			
				foreach($cols as $col) {
					//TODO: EL COL SPAN.
					$tdmod = '';
					if (isset($col['align'])) {
						$align = new bas_htm_tdalign($col['align']);
						$tdmod .= ' ' . $align->htmltext;
					} elseif (isset($col['dynamic_align'])) {
					eval("\$align = new bas_htm_tdalign(\"{$col['dynamic_align']}\");");
					$tdmod .= ' ' . $align->htmltext;							
					}
					
					$data = isset($rec[$col['id']]) ? $rec[$col['id']]: '';
					if (isset($col['template'])) {
//						$data = $template->stamp($this->tabledef, $col['id'], $rec);
						$data = 'TPT';
					} elseif (isset($col['format'])) {
						$format = new bas_dat_format($col['format']);
						$data = $format->format($data);
					} elseif (isset($col['dynamic_format'])){
						eval("\$format = new bas_dat_format(\"{$col['dynamic_format']}\");");
						$data = $format->format($data);
					}
				
					if (($ncol >= $ini && $ncol <= $fin) && ($nrow >=$inic && $nrow <=$finc)){
						$content[$ncol] = $data;
					}
					$ncol++;
				}
				$fpdf->tableRow($content, 'Helvetica','', 8);	
				$rec=$this->dataset->next();
				$nrow++;
				
		}
	
	}
	
	
	public function pdfme(&$fpdf, $ncolpag=5, $nepag = 12, $ncolmax=16, $nemax=80){
		
		if ($this->dataset->errormsg){
			$fpdf->write("Error en el dataset");
			$fpdf->write("Error: {$this->dataset->errormsg}");
			
		} else {
		
			$cols=array();
			foreach($this->extension as $extension){
				if ($extension == '*') $cols = array_merge($cols, $this->tabledef->getcols());
				else $cols = array_merge($cols, $extension->getcols());
			}
			$fpdf->Header('','Titulo del Documento','');
			//$fpdf->setcols($colwidths = $this->tabledef->getproperty('*','colwidth', 50));
						
			//cabecera de la tabla
			
			/*if ($this->tabledef->hashead){
				$icol = 0;
				$content = array();
				foreach($cols as $col) {
					$data = $col['caption'];
					if (isset($col['headtemplate'])) eval ("\$data=\"${col['headtemplate']}\";");
					$content[$icol] = $data;
					$icol++;
				}
				$fpdf->tableBegin($content);
			}*/

			
			
			// $template = new bas_pdf_template(); TODO: PDF_TEMPLATE
			//contenido de la tabla
			/*$rec = $this->dataset->reset();
			if (method_exists($this->tabledef, 'getrecordkey'))	$currkey = $this->tabledef->getrecordkey();
			else $currkey = false;
			
			$nrow = 0;
			while ($rec){
				foreach($this->extension as $extension){
					if ($extension != '*') $extension->mergedata($rec);
				}
				
				unset($content); $content = array(); 
				$ncol = 0;
				foreach($cols as $col) {
					//TODO: EL COL SPAN.
					$tdmod = '';
					if (isset($col['align'])) {
						$align = new bas_htm_tdalign($col['align']);
						$tdmod .= ' ' . $align->htmltext;
					} elseif (isset($col['dynamic_align'])) {
						eval("\$align = new bas_htm_tdalign(\"{$col['dynamic_align']}\");");
						$tdmod .= ' ' . $align->htmltext;							
					}
					
					$data = isset($rec[$col['id']]) ? $rec[$col['id']]: '';
					if (isset($col['template'])) {
//						$data = $template->stamp($this->tabledef, $col['id'], $rec);
						$data = 'TPT';
					} elseif (isset($col['format'])) {
						$format = new bas_dat_format($col['format']);
						$data = $format->format($data);
					} elseif (isset($col['dynamic_format'])){
						eval("\$format = new bas_dat_format(\"{$col['dynamic_format']}\");");
						$data = $format->format($data);
					}

					$content[$ncol] = $data;
					$ncol++;
				}
				$fpdf->tableRow($content);
				$rec=$this->dataset->next();
				$nrow++;
			}*/

			

			$neini = 0;
			$nefin = $nepag;

		
			$vec = array();
			
			$i=0;	
			$ncolini = 0;
			$ncolfin = 5;

			
			while($nefin <= $nemax){
				while($ncolini < $ncolmax){
					$this->cabecera($ncolini,$ncolfin,&$fpdf,$cols);
					$this->cuerpo($ncolini,$ncolfin,&$fpdf,$cols,$neini,$nefin);
					$fpdf->AliasNbPages();
					$fpdf->AddPage();
					$ncolini = $ncolfin + 1;
					$ncolfin += $ncolpag;
				}
				$ncolini = 0;
				$ncolfin = $ncolpag;
				$neini = $nefin + 1;
				$nefin += $nepag;
			}

			//footer.
			/*
			if ($this->tabledef->hasfoot){
				unset($content); $content = array(); 
				$icol = 0;
				foreach($cols as $col) {
					$data = $col['foot'];
					if (isset($col['foottemplate'])) eval ("\$data=\"${col['foottemplate']}\";");
					$content[$icol] = $data;
					$icol++;
				}
				$fpdf->tableRow($content);
			}*/
			
			$fpdf->tableEnd();
		}
	}
}
?>
