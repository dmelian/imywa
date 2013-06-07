<?php

class bas_pdf_miclase extends bas_pdf_form{
	protected $Resultquery=array();
	protected $mix;
	protected $miy;
	protected $myhead=array();
	protected $totalrow;
	protected $totalheight;
	
	function load($frame){
		$rows=$frame->get_Allrows();
		$this->mix=count($rows);//numero de filas
		$this->miy=count($frame->components);//numero de columnas
		$resultindex=0;
		for($i=0;$i<$this->miy;$i++){
			$component=$frame->getComponent($i);
			$this->myhead[$i]= $this->transformData($component->caption);

			for($j=0;$j<$this->mix;$j++){
				if (isset($rows[$j]) && isset($rows[$j][$component->id])){
					if(!empty($rows[$j][$component->id])){
					$this->Resultquery[$j][$i][0]= $this->transformData($component->OnFormat($rows[$j][$component->id]));
					$this->Resultquery[$j][$i][1]=$component->type;
					}
					else{
						$this->Resultquery[$j][$i][0]= $this->transformData($component->OnFormat(''));
						$this->Resultquery[$j][$i][1]=$component->type;
					}
				} 
				else {
					$this->Resultquery[$j][$i][0]= $this->transformData($component->OnFormat(''));
					$this->Resultquery[$j][$i][1]="unknow";
				} 
			}
		}
	}
	protected function transformData($str){
		return iconv('UTF-8', 'windows-1252',$str);
	}
	
	protected function interprete($image){
		$mistring=$image;
		$count=strpos($mistring,']');
		$selector=intval(substr($mistring,$count+1,1));
		$mistring=substr($mistring,1,$count-1);
		$mistring=explode(',',$mistring);
		$option=intval($mistring[$selector]);
		switch($option){
			case 3: return "Alarma";
			case 6: return "Error";
			case 5: return "A. Temporal";
			case 7: return "X";
			case 8: return "O.K.";
			Default: return "";
		}
	}
	/*function loadheader($db, $dbtable){
		global $_APPLICATION;
		$link= mysqli_connect('localhost',$_APPLICATION->user,$_APPLICATION->password,$db);
		$stmt= mysqli_query($link,"SELECT * FROM $dbtable");
		$imyhead=0;
		$field_info = mysqli_fetch_fields($stmt);
		foreach($field_info as $valor){
			$this->myhead[$imyhead]=$valor->name;
			$imyhead++;
		}
		mysqli_free_result($stmt);
		mysqli_close($link);
	}

	function load($db, $dbtable){
		global $_APPLICATION;
		
		$link= mysqli_connect('localhost',$_APPLICATION->user,$_APPLICATION->password,$db);
		$stmt= mysqli_query($link,"SELECT COUNT(*) FROM $dbtable");
		$total=mysqli_fetch_array($stmt);
		$this->mix= (int) $total[0];//cantidad de filas total en la tabla
		$stmt= mysqli_query($link,"SELECT * FROM $dbtable");
		$this->miy= mysqli_field_count($link);//numero total columnas
		$iMatrix=0;
		while($result = mysqli_fetch_array($stmt)){
			for($jMatrix=0;$jMatrix<$this->miy;$jMatrix++){
				$this->Resultquery[$iMatrix][$jMatrix]=$result[$jMatrix];//rellenamos la matriz
			}
			$iMatrix++;
		}
		mysqli_close($link);
		mysqli_free_result($stmt);
	}*/
	
	function mypage($iinicio, $ifin, $jinicio, $jfin,$pdf){
		global $_LOG;
		$pdf->SetFillColor(149,184,201);//caracteristicas del texo de la tabla. this
		$pdf->SetTextColor(0);//this
		$pdf->SetFont('');//this
		$pdf->SetDrawColor(0,0,0);//this
		$pdf->SetLineWidth(.3);//this
		$fill = true; //parametro que nos servirá para colorear las celdas
		$alto=parent::$heightrow;
		$ancho=parent::$widthrow;
		/*imprimimos la cabecera*/
		//siempre la primera celda es la id
		$pdf->Cell($ancho,$alto,$this->myhead[0],1,0,'L',$fill);//this
		for($j=$jinicio; $j < $jfin; $j++){
			$pdf->Cell($ancho,$alto,$this->myhead[$j],1,0,'L',$fill);//this
		}
		$pdf->Ln();//nueva linea this
		$fill=false;
		
		$pdf->SetFillColor(224,235,255);//caracteristicas del texo de la tabla. this
		$pdf->SetTextColor(0);//this
		$pdf->SetFont('');//this
		$pdf->SetDrawColor(0,0,0);//this
		$pdf->SetLineWidth(.3);//this
		
		$textolargo="";
		for($i=$iinicio; $i < $ifin; $i++){
			$h=$alto*$this->totalheight;//esta va a ser la altura definida para todas las celdas por lo tanto todas las pags serán uniformes
			$vartexto =$this->Resultquery[$i][0][0];//este es el identificador de la primera columna
			$x=$pdf->GetX();//this
			$y=$pdf->GetY();//this
			//Draw the border
			if($fill)$pdf->Rect($x,$y,$ancho,$h,'DF');//this
			else $pdf->Rect($x,$y,$ancho,$h);//this
			//imprimimos el texto
			if(($this->Resultquery[$i][0][1]=="money")||($this->Resultquery[$i][0][1]=="number")){
				$vartexto=number_format(floatval($vartexto),2,',','.');
				$pdf->MultiCell($ancho,$alto,$vartexto,0,'R');
			}
			else if($this->Resultquery[$i][0][1]=="boolean"){
				  if(intval($vartexto)==0){
					$pdf->MultiCell($ancho,$alto,'No',0,'L');
				  }
				  else{
					$pdf->MultiCell($ancho,$alto,utf8_decode('Sí'),0,'L');
				  }
			}
			else{
				$pdf->MultiCell($ancho,$alto,$vartexto,0,'L');//this
			}
			//Ponemos la posicion en la dercha de la celda
			$pdf->SetXY($x+$ancho,$y);//this
			for($j=$jinicio; $j < $jfin; $j++){
				$vartexto =$this->Resultquery[$i][$j][0];
				//$_LOG->debug("RESULTQUERY",$this->Resultquery[$i][$j][0]);
				//$_LOG->debug("VARTEXTO",$vartexto);
				$x=$pdf->GetX();//this
				$y=$pdf->GetY();//this
				//Dibujamos el borde
				if($fill)$pdf->Rect($x,$y,$ancho,$h,'DF');//this
				else $pdf->Rect($x,$y,$ancho,$h);//this
				//Imprimimos el texto
				if(($this->Resultquery[$i][$j][1]=="money")||($this->Resultquery[$i][$j][1]=="number")){
					$vartexto=number_format(floatval($vartexto),2,',','.');
					$pdf->MultiCell($ancho,$alto,$vartexto,0,'R');
				}
				else if($this->Resultquery[$i][$j][1]=="boolean"){
					if(intval($vartexto)==0){
						$pdf->MultiCell($ancho,$alto,'No',0,'L');
					}
					else{
						$pdf->MultiCell($ancho,$alto,utf8_decode('Sí'),0,'L');
					}
				}
				else if($this->Resultquery[$i][$j][1]=="image"){
					$vartexto=$this->interprete($vartexto);
					$pdf->MultiCell($ancho,$alto,$vartexto,0,'L');//this
				}
				else{
					$pdf->MultiCell($ancho,$alto,$vartexto,0,'L');//this
				}
				//Ponemos la posicion a la derecha de la celda
				$pdf->SetXY($x+$ancho,$y);//this
				if($j==$jfin-1)$pdf->Ln($h);//this
			}
			$fill = !$fill;
		}
	}
	
	function Onprint($pdf){
		$iinicio=0;
		$ifin=$this->totalrow;
		$jinicio=1;
		$column=parent::$ncolumns;
		$jfin=$column+1;

		if((($this->miy-1)%$column)==0){//calculamos el número necesario de paginas para sacar la tabla completa horizontalmente es decir el numero de paginas que necesitaremos para poner todos los campos de la tabla
			$npags=(int)(($this->miy-1)/$column);
		}
		else{
			$npags=(int) (($this->miy-1)/$column);
			$npags++;
		}

		if(($this->mix%$this->totalrow)==0){ //calculamos el numero de veces que se tiene que repetir las paginas para tener la tabla completa.
			$nveces=(int)($this->mix/$this->totalrow);
		}
		else{
			$nveces=(int)($this->mix/$this->totalrow);
			$nveces++;
		}

		for($a=0;$a<$nveces;$a++){//imprimimos las paginas
			for($b=0;$b<$npags;$b++){
				if($jfin>$this->miy)$jfin=$this->miy;
				if($ifin>$this->mix)$ifin=$this->mix;
				$this->mypage($iinicio,$ifin,$jinicio,$jfin,$pdf);
				if($b != $npags-1) $pdf->Addpage();//si no lo controlamos se genera una pagina extra que no nos interesa hay que cortarlo en la ultima iteración this
				$jinicio+=$column;
				$jfin+=$column;
			}
			$jinicio=1;
			$jfin=$column+1;
			if($a != $nveces-1) $pdf->Addpage(); //se genera una pagina extra que no nos interesa. this
			$iinicio+=$this->totalrow;
			$ifin+=$this->totalrow;
		}
	}
	
	function adjustment($pdf){
		$nlines=0;
		$wr=parent::$widthrow;
		$hp=parent::$heightpage;
		$hr=parent::$heightrow;
		for($irow=0;$irow<$this->mix;$irow++){
			for($jcol=0;$jcol<$this->miy;$jcol++){
				$nlines=max($nlines,$pdf->NbLines($wr,$this->Resultquery[$irow][$jcol][0]));//calculamos la altura máxima.this
			}
		}
		if($nlines==0)$nlines=1;
		$this->totalheight=$nlines;
		$this->totalrow=(int)($hp/($nlines*$hr));
	}
	
	function OnPdf($pdf,$frame){
	//function Onpdf($db,$dbtable){
		//$this->loadheader($db,$dbtable); //cargamos la cabecera le tendremos que pasar la tabla
		//$this->load($db,$dbtable); //cargamos los datos de la tabla de datos esta funcion y la anterior deberian ir juntas pero me da fallos si las junto. le pasaremos la tabla.
		$this->load($frame);
		$pdf->loadtitle($frame->title);//this
		$pdf->SetFont('Arial','',6); //definimos la letra y el tamaño de la letra
		//$this->beginDoc();
		$this->adjustment($pdf);
		$this->Onprint($pdf);
		//$this->endDoc();
	}
	
}

?>