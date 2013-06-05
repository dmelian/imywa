<?php

class bas_pdf_card extends bas_pdf_form{
	public $cellwidth;
	public $cellheigth;
	private $micard;
	private $gridwidth;
	private $gridheight;
	//private $kheigth;
	
	function loadcard($variabletipocard){//I think this function could be the __construct
		//$this->micard= new bas_frmx_cardframe();
		$this->micard=$variabletipocard;
	}
	
	function adjust($x=3,$y=5){//the grid on the paper.x=>width; y=>height
		$wp=parent::$widthpage;
		$hp=parent::$heightpage;
		$this->gridwidth=$x;
		$this->gridheight=$y;
		$this->cellwidth=$wp/($x*2);
		$this->cellheigth=$hp/($y);
	}
	
	function pagetitle($title,$pdf){ //Print the title on the top of the page
		$alto=parent::$heightrow;
		$ancho=parent::$widthpage;
		$pdf->SetFont('Arial',"BU",10);//this
		$pdf->MultiCell($ancho,$alto,$title,0,'C');//this
		$pdf->Ln($alto);//this
	}
	
	function Onprint($pdf){//print the info from card into a pdf
		$anchofijo=$this->cellwidth;
		$alto=parent::$heightrow;
		$vectororder=$this->micard->sortComponents();
		$ttab=count($this->micard->tabs);//total tabs
		$tabs=$this->micard->tabs;
		$kheigth=0;
		for($tab=0;$tab<$ttab;$tab++){
			if($tab!=0)$pdf->AddPage();
			$indicetab=$tabs[$tab];
			$this->pagetitle($indicetab,$pdf);
			$index=0;
			
			$ilength=$this->gridheight;
			$jlength=$this->gridwidth;
			for($i=1;$i<=$ilength;$i++){
				$kheigth=0;
				for($j=1;$j<=$jlength;$j++){
					$index=$vectororder[$indicetab][$i][$j];
					if(($index!=-1)&&($index!=-2)){
						$campo=  $this->micard->getComponent($index);//$this->micard->components[$index]["field"];
						$label=$campo->caption;
						//$kwidth=$this->micard->components[$index]["width"];
						$kwidth=1;
						$id = $this->micard->getComponent($index)->id;
						//$value=utf8_decode("pepe");
						if (isset($this->micard->record->current[$campo->id]))$value=utf8_decode($this->micard->record->current[$campo->id]);
						else $value=utf8_decode("");
						$kheigth=max($kheigth,$pdf->NbLines($this->cellwidth*$kwidth,$value));//this
						$kheigth=max($kheigth,$pdf->NbLines($this->cellwidth*$kwidth,$label));//this
					}
				}
				for($j=1;$j<=$jlength;$j++){
					$index=$vectororder[$indicetab][$i][$j];
					if($index!=-1){
						if($index==-2){//empty space
							$x=$pdf->GetX();//this
							$y=$pdf->GetY();//this
							$pdf->MultiCell(2*$anchofijo,$alto,"",0);//$this->
							$pdf->SetXY($x+$anchofijo*2,$y);//$this->
						}else if($index>=0){
							$campo= $this->micard->getComponent($index);//$this->micard->components[$index]["field"];
							
							//$value=$campo->format($this->micard->record->current[$campo->id]);//obtendria el formato de la información.$campo->value
							if (isset($this->micard->record->current[$id]))$value=utf8_decode($this->micard->record->current[$campo->id]);
							else $value=utf8_decode("");
// 							$value=$this->micard->record->current[$campo->id];
							$label=$campo->caption;
							$type=$campo->type;
							//$kwidth=$this->micard->components[$index]["width"];
							$kwidth=1;
							//}
							switch($type){
								case "boolean":{
									$x=$pdf->GetX();//$this->
									$y=$pdf->GetY();//this
									//$this->imagencelda($label,$value,$kwidth);
									$ancho=$anchofijo*$kwidth;
									$pdf->SetFont('Arial','',9);//$this
									$x=$pdf->GetX();//this
									$y=$pdf->GetY();//this
									$pdf->MultiCell($ancho,$alto,$label,0);//$pdf->MultiCell($ancho,$alto*$kheigth,$label,0);//$this->
									$pdf->SetXY($x+$ancho,$y);//$this->
									$x=$pdf->GetX();//this
									$y=$pdf->GetY();//this
									if($value==true){
										$pdf->SetFillColor(0);//this
										$pdf->Rect($x,$y,4,4,'FD');//this
										$pdf->MultiCell($ancho,$alto,"",0);//$pdf->MultiCell($ancho,$alto*$kheigth,"",0);//this
									}
									else{
										$pdf->Rect($x,$y,4,4,'D');//this
										$pdf->MultiCell($ancho,$alto,"",0);//$pdf->MultiCell($ancho,$alto*$kheigth,"",0);//this
									}
									$pdf->SetXY($x+$ancho,$y);//this
									break;
								}
								default:{
									$x=$pdf->GetX();//this
									$y=$pdf->GetY();//this
									$ancho=$anchofijo*$kwidth;
									$pdf->SetFont('Arial','',9);//this->
									$x=$pdf->GetX();//this
									$y=$pdf->GetY();//this
									$pdf->MultiCell($ancho,$alto,utf8_decode($label),0);//($ancho,$alto*$this->kheigth,utf8_decode($label),0);this
									$pdf->SetXY($x+$ancho,$y);//this
									$x=$pdf->GetX();//this
									$y=$pdf->GetY();//this
									/*if($editable==false){
										$this->SetFillColor(127,127,127);
										$this->MultiCell($ancho,$alto,$value,1,'L',true);
									}
									else */
										$pdf->Rect($x,$y,$ancho,$alto*$kheigth);//this
										$pdf->MultiCell($ancho,$alto,utf8_decode($value),0);//$pdf->MultiCell($ancho,$alto*$kheigth,utf8_decode($value),0);//this
									$pdf->SetXY($x+$ancho,$y);//this
								}
							}
						}
					}
				}
				$salto=$alto*$kheigth;
				$salto+=1;
				$pdf->Ln($salto);//this
			}
		}
	}
	
	function OnPdf($pdf, $card){
		$this->loadcard($card);
		$pdf->loadtitle($card->title);//this
		$pdf->SetFont('Arial','',9);
		$this->adjust($card->grid["width"],$card->grid["height"]);
		//$this->beginDoc();
		$this->Onprint($pdf);
		//$this->endDoc();
	}
	
	
}
?>
