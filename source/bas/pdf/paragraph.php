<?php

class bas_pdf_paragraph extends bas_pdf_form{
	private $form;
	
	public function __construct(&$form){
		$this->form= $form;
	}
	
	function setFont($family, $size, $color=0,$style=""){
		$estilo="";
		$style2=" ".$style;//I add a space because the strpos the first time return 0, because it is the position where it starts.
		if((strlen($style))>0){
			if((strpos($style2,"bold"))>0)$estilo=$estilo."B";
			if((strpos($style2,"underline"))>0)$estilo=$estilo."U";
			if((strpos($style2,"italic"))>0)$estilo=$estilo."I";
		}
		switch ($family){
			case 'serif': $family= 'times'; break;
			case 'sansserif': $family= 'helvetica'; break;
			case 'monospace': $family= 'courier'; break;
		}
		$this->form->SetFont($family,$estilo,$size);
		if((strlen($color))>0){
			if((strpos($color,','))>0){//probar si coge la coma o no.
				$aux;
				$auxstr="";
				$aux=strpos($color,",");
				$red=(int)substr($color,0,$aux);
				$auxstr=substr($color,$aux+1);
				$aux=strpos($auxstr,",");
				$green=(int)substr($auxstr,0,$aux);
				$auxstr=substr($auxstr,$aux+1);
				$blue=(int)$auxstr;
				$this->form->SetTextColor($red,$green,$blue);
			}else{
				$intcolor=(int)$color;//grey scale, because you only put one argument.
				$this->form->SetTextColor($intcolor);
			}
		}
	}
	
	function forwardpage(){
		$this->form->AddPage();
	}
	
	function text($text, $align="left", $style="", $indent=true){
		$text=utf8_decode($text);
		$height=5*($this->form->NbLines(190,$text));
		$width=190;
		$align2="";
		if($indent==true){
			$text="       ".$text;//why doesnt work "/t"???
		}
		if($align=="left"){
			$align2="L";
		}else if($align=="right"){
			$align2="R";
		}else if($align=="center"){
			$align2="C";
		}else if($align=="justify"){
			$align2="J";
		}
		if((strlen($style))>0){
			$estilo="";
			$style2=" ".$style;
			if((strpos($style2,"bold"))>0)$estilo=$estilo."B";
			if((strpos($style2,"underline"))>0)$estilo=$estilo."U";
			if((strpos($style2,"italic"))>0)$estilo=$estilo."I";
			$this->form->SetFont("",$estilo);
		}
		$x=$this->form->GetX();
		$y=$this->form->GetY();
		if($this->form->GetY()+$height>$this->form->PageBreakTrigger)$this->form->AddPage($this->form->CurOrientation);
		//$this->Rect($x,$y,$width,$height);
		$this->form->MultiCell($width,5,$text,0,$align2);
		$this->form->Ln();
	}
	
	function image($url, $align=""){
		$this->form->Image($url);
		$this->form->Ln();
		/*de momento no se me ocurre nada para el align*/
	}
	
	function Onprint(){
		$total=count($this->result);
		for($i=0;$i<$total;$i++){
			switch($this->result[$i][0]){
				case "fowardpage":{
					$this->forwardpage();
					break;
				}
				case "image":{
					if($this->result[$i][2]!=""){
						$this->image($this->result[$i][1],$this->result[$i][2]);
					}else{
						$this->image($this->result[$i][1]);
					}
					break;
				}
				case "text":{
					if($this->result[$i][2]!=""){
						if($this->result[$i][3]!=""){
							$this->text($this->result[$i][1],$this->result[$i][2],$this->result[$i][3]);
						}else{
							$this->text($this->result[$i][1],$this->result[$i][2]);
						}
					}else if($this->result[$i][3]!=""){
						$this->text($this->result[$i][1],"",$this->result[$i][3]);
					}else{
						$this->text($this->result[$i][1]);
					}
					break;
				}
				case "setFont":{
					if($this->result[$i][3]!=""){
						if($this->result[$i][4]!=""){
							$this->setFont($this->result[$i][1],$this->result[$i][2],$this->result[$i][3],$this->result[$i][4]);
							
						}else{
							$this->setFont($this->result[$i][1],$this->result[$i][2],$this->result[$i][3]);
						}
					}else if($this->result[$i][4]!=""){
						$this->setFont($this->result[$i][1],$this->result[$i][2],0,$this->result[$i][4]);
					}else{
						$this->setFont($this->result[$i][1],$this->result[$i][2]);
					}
					break;
				}
			}
		}
	}
	
	function addorder($properties=array()){
		$total=count($properties);
		$order=$properties[0];
		/*for($irow=0;$irow<$total-1;$irow++){
			$properties[$irow]=$properties[$irow+1];
		}
		$properties[$total]=NULL;
		$total=count($properties);*/
		if(count($this->result)>0)$index=count($this->result);
		else $index=0;
		switch($order){
			case "text":{
				if($total==2){
					$this->result[$index][0]=$order;
					$this->result[$index][1]=$properties[1];
					$this->result[$index][2]="";
					$this->result[$index][3]="";
					$this->result[$index][4]="";
				}else if($total==3){
					$this->result[$index][0]=$order;
					$this->result[$index][1]=$properties[1];
					$this->result[$index][2]=$properties[2];
					$this->result[$index][3]="";
					$this->result[$index][4]="";
				}else if($total==4){
					$this->result[$index][0]=$order;
					$this->result[$index][1]=$properties[1];
					$this->result[$index][2]=$properties[2];
					$this->result[$index][3]=$properties[3];
					$this->result[$index][4]="";
				}
				break;
			}
			case "setFont":{
				if($total==3){
					$this->result[$index][0]=$order;
					$this->result[$index][1]=$properties[1];
					$this->result[$index][2]=$properties[2];
					$this->result[$index][3]="";
					$this->result[$index][4]="";
				}else if($total==4){
					$this->result[$index][0]=$order;
					$this->result[$index][1]=$properties[1];
					$this->result[$index][2]=$properties[2];
					$this->result[$index][3]=$properties[3];
					$this->result[$index][4]="";
				}else if($total==5){
					$this->result[$index][0]=$order;
					$this->result[$index][1]=$properties[1];
					$this->result[$index][2]=$properties[2];
					$this->result[$index][3]=$properties[3];
					$this->result[$index][4]=$properties[4];
				}
				break;
			}
			case "fowardpage":{
				$this->result[$index][0]=$order;
				$this->result[$index][1]="";
				$this->result[$index][2]="";
				$this->result[$index][3]="";
				$this->result[$index][4]="";
				break;
			}
			case "image":{
				if($total==2){
					$this->result[$index][0]=$order;
					$this->result[$index][1]=$properties[1];
					$this->result[$index][2]="";
					$this->result[$index][3]="";
					$this->result[$index][4]="";
				}else{
					$this->result[$index][0]=$order;
					$this->result[$index][1]=$properties[1];
					$this->result[$index][2]=$properties[2];
					$this->result[$index][3]="";
					$this->result[$index][4]="";
				}
				break;
			}
		}
	}
}

?>