<?php
class bas_pdf_form extends lib_fpdf_fpdf{
	public $title;
	protected static $heightrow=5;
	protected static $widthrow=38;
	protected static $widthpage=190;
	protected static $heightpage=240;
	protected static $ncolumns=4;
	
	function loadtitle($title){
		$this->title=$title;
	}

	function Header(){
		$this->SetFillColor(70,130,180);//caracteristicas del texo de la tabla.
		$this->SetTextColor(0);
		$this->SetFont('');
		$this->SetFont('Arial','',8);
		$day= date("d");
		$nday=date("l");
		$month=date("m");
		$year=date("Y");
		switch($nday){
			case "Monday":
				$nday="Lunes";
				break;
			case "Tuesday":
				$nday="Martes";
				break;
			case "Wednesday":
				$nday="Miércoles";
				break;
			case "Thursday":
				$nday="Jueves";
				break;
			case "Friday":
				$nday="Viernes";
				break;
			case "Saturday":
				$nday="Sábado";
				break;
			case "Sunday":
				$nday="Domingo";
				break;
		}
		switch($month){
			case 1:
				$month="Enero";
				break;
			case 2:
				$month="Febrero";
				break;
			case 3:
				$month="Marzo";
				break;
			case 4:
				$month="Abril";
				break;
			case 5:
				$month="Mayo";
				break;
			case 6:
				$month="Junio";
				break;
			case 7:
				$month="Julio";
				break;
			case 8:
				$month="Agosto";
				break;
			case 9:
				$month="Septiembre";
				break;
			case 10:
				$month="Octubre";
				break;
			case 11:
				$month="Noviembre";
				break;
			case 12:
				$month="Diciembre";
				break;
		}
		$nday=utf8_decode($nday);
		$month=utf8_decode($month);
		
		$fecha= $nday.",".$day." de ".$month." de ".$year.".";
		$title=$this->title;//"mi titulo";
		$title= strtoupper($title);
		$x=$this->GetX();
		$y=$this->GetY();
		$xinicio=$x;
		$yinicio=$y;
		$this->Rect($x,$y,95,15,'F');
		//($ancho,$alto,$this->myhead[0],1,0,'L',$fill);
		$this->Cell(95,15,$title,0,0,'L');
		$this->SetXY($x+95,$y);
		//cliente
		GLOBAL $_SESSION;
		/*esbozo sobre el cliente y su nif
			select * from cliente;
			coger el dato partitionValue;
			y despues intentar ir con a empresa y recoger el nif y el nombre....
		*/
		$x=$this->GetX();
		$y=$this->GetY();
		$this->Rect($x,$y,95,5,'F');
		$this->Cell(95,5,"CLIENTE: ",0,0,'R');
		$this->Ln(5);
		//CIF
		$x=$this->GetX();
		$y=$this->GetY();
		$this->SetXY($x+95,$y);
		$x=$this->GetX();
		$this->Rect($x,$y,95,5,'F');
		$this->Cell(95,5,"CIF/DNI:",0,0,'R');
		//Usuario
		
		$this->Ln(5);
		$x=$this->GetX();
		$y=$this->GetY();
		$this->SetXY($x+95,$y);
		$x=$this->GetX();
		$this->Rect($x,$y,95,5,'F');
		$this->Cell(95,5,"USUARIO: ".$_SESSION->user,0,0,'R');
		
		$this->SetXY($xinicio,$yinicio);
		$this->Ln(16);
		//$this->SetXY($x,$y);
		$x=$this->GetX();
		$y=$this->GetY();
		$this->Rect($x,$y,190,3,'F');
		$this->Cell(190,3,$fecha,0,0,'L');
		$this->Ln();
	}

	function NbLines($w,$txt){
		//Computes the number of lines a MultiCell of width w will take
		$cw=&$this->CurrentFont['cw'];
		if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		$s=str_replace("\r",'',$txt);
		$nb=strlen($s);
		if($nb>0 and $s[$nb-1]=="\n")
			$nb--;
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$nl=1;
		while($i<$nb)
		{
			$c=$s[$i];
			if($c=="\n")
			{
				$i++;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
				continue;
			}
			if($c==' ')
				$sep=$i;
			$l+=$cw[$c];
			if($l>$wmax)
			{
				if($sep==-1)
				{
					if($i==$j)
						$i++;
				}
				else
					$i=$sep+1;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
			}
			else
				$i++;
		}
		return $nl;
	}
	
	public function beginDoc(){
		$this->SetAutoPageBreak(true);
		$this->AddPage();
		
	}
	
	public function endDoc(){
		$this->Output();
	}
	
	public function endparagraph(){
		$texto="En cumplimiento de lo establecido en la Ley Organica 15/99 de Protección de Datos Carácter Personal que sus datos personales quedarán incorporados y serán tratados en los fichero de A&M EDITA S.L. Asimismo, le informamos de la posiblidad de que ejerza los derechos de acceso, rectificación, cancelación y oposición en la siguiente dirección C/ Pío XII, 64 local 5.";
		$texto2="En caso de que este documento deban incluirse datos de carácter personal referentes a personas físicas o juridicas distintas de quien lo presente deberá con carácter previo a su inclusión, informales de los extremos contenidos  en el parrafo anterior. Asimismo, se prohibe el uso, divulgación y tratamiento de la información contenida en dicho documento por terceras partes no autorizadas al efecto.";
		$texto = utf8_decode($texto);
		$texto2= utf8_decode($texto2);
		$this->Write(3,$texto);
		$this->Ln();
		$this->Write(3,$texto2);
		$this->Ln();
	}
	
	
	public function Footer(){
		$this->SetY(-23);
		$texto="En cumplimiento de lo establecido en la Ley Organica 15/99 de Protección de Datos Carácter Personal que sus datos personales quedarán incorporados y serán tratados en los fichero de A&M EDITA S.L. Asimismo, le informamos de la posiblidad de que ejerza los derechos de acceso, rectificación, cancelación y oposición en la siguiente dirección C/ Pío XII, 64 local 5.";
		$texto2="En caso de que este documento deban incluirse datos de carácter personal referentes a personas físicas o juridicas distintas de quien lo presente deberá con carácter previo a su inclusión, informales de los extremos contenidos  en el parrafo anterior. Asimismo, se prohibe el uso, divulgación y tratamiento de la información contenida en dicho documento por terceras partes no autorizadas al efecto.";
		$texto = utf8_decode($texto);
		$texto2= utf8_decode($texto2);
		$this->Write(3,$texto);
		$this->Ln();
		$this->Write(3,$texto2);
		$this->Ln();
		global $_SESSION;
		//if($_SESSION)
		$this->SetFillColor(255,255,255);//caracteristicas del texo de la tabla.
		$this->SetTextColor(0);
		$this->SetFont('');
		$this->SetDrawColor(128,0,0);
		$this->SetLineWidth(.3);
		$this->SetFont('Arial','',8);
		$x=$this->GetX();
		$y=$this->GetY();
		$hr=self::$heightrow;
		$this->Rect($x,$y,47,$hr,'F');
		$this->Cell(47,$hr,"A&M EDITA S.L.",0,0,'L');
		$this->SetXY($x+47,$y);
		$x=$this->GetX();
		$this->Rect($x,$y,47,$hr,'F');
		$this->Cell(47,$hr,"TELEFONO",0,0,'L');
		$this->SetXY($x+47,$y);
		$x=$this->GetX();
		$this->Rect($x,$y,47,$hr,'F');
		$this->Cell(47,$hr,"FAX",0,0,'L');
		$this->SetXY($x+47,$y);
		$x=$this->GetX();
		$this->Rect($x,$y,49,$hr,'F');
		$this->Cell(49,$hr,"WEB",0,0,'L');
		$this->Ln($hr);
		$this->SetFillColor(70,130,180);//caracteristicas del texo de la tabla.
		$this->SetTextColor(255);
		$x=$this->GetX();
		$y=$this->GetY();
		$this->Rect($x,$y,47,$hr,'F');
		$this->Cell(47,$hr,"C/ PIO XII, 64 (LOCAL B)",0,0,'L',true);
		$this->SetXY($x+47,$y);
		$x=$this->GetX();
		$this->Rect($x,$y,47,$hr,'F');
		$this->Cell(47,$hr,"928 260 540",0,0,'L',true);
		$this->SetXY($x+47,$y);
		$x=$this->GetX();
		$this->Rect($x,$y,47,$hr,'F');
		$this->Cell(47,$hr,"928 494 669",0,0,'L',true);
		$this->SetXY($x+47,$y);
		$x=$this->GetX();
		$this->Rect($x,$y,49,$hr,'F');
		$this->Cell(49,$hr,"www.amedita.es",0,0,'L',true);
	}
	
}
?>