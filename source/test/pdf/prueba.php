<?php

class pdf_prueba extends bas_frm_emptyhtml{
	private $pdf_mode='pdf1';
	
	public function OnPdf1(){
		
		/*$mivar= new bas_pdf_miclase(); //creamos una instancia de la clase.
		$mivar->loadheader('contaluz',"concepto"); //cargamos la cabecera le tendremos que pasar la tabla
		$mivar->load('contaluz',"concepto"); //cargamos los datos de la tabla de datos esta funcion y la anterior deberian ir juntas pero me da fallos si las junto. le pasaremos la tabla.
		$mivar->SetFont('Arial','',6); //definimos la letra y el tamaño de la letra
		$mivar->SetAutoPageBreak(true);//para que se genere automaticamente el cambio de pagina.
		$mivar->AddPage();//primera pagina para escribir
		$mivar->adjustment();
		$mivar->Onprint();//imprimos la tabla
		$mivar->Output();//generamos el documento saliente.*/
		$mivar= new bas_pdf_miclase();
		$mivar->Onpdf('contaluz','concepto');
		
	}
	public function OnPdf2(){
		/*$miobjeto= new bas_pdf_card();
		$miobjeto->adjust(4,4);
		$miobjeto->beginDoc();
		$miobjeto->pagetitle("Esto es un Titulo de Ejemplo");
		$y=$miobjeto->GetY();
		$x=$miobjeto->GetX();
		$ancho=$miobjeto->cellwidth*2;//tener cuidado seems legit.
		$miobjeto->input("Usuario","Santiago","",1);
		$miobjeto->SetXY($x+$ancho,$y);//$miobjeto->SetXY($x+$ancho*2,$y);
		$x=$miobjeto->GetX();
		$y=$miobjeto->GetY();
		$miobjeto->emptySpace();
		$miobjeto->SetXY($x+$ancho,$y);
		$x=$miobjeto->GetX();
		$y=$miobjeto->GetY();
		$miobjeto->input("E-mail","micorreo@gmail.com",true,2);
		$miobjeto->SetXY($x+$ancho,$y);
		$miobjeto->Ln();
		$miobjeto->imagencelda("Male",true);
		$miobjeto->imagencelda("Female",false);
		$miobjeto->endDoc();*/
		$micard= new bas_frmx_cardframe(12,$tabs='', $grid=array('width'=>2,'height'=>2));
		$x=$y=$width= $height = 1;
		$micard->tabs = array("1","2");
		$micard->addComponent("1", 1, 2, 2, $height, new bas_sqlx_fieldtext("N_Factura","text","fact","Numero de Factura",false,"val",true));
		$micard->addComponent("2", 2, 2, $width, $height, new bas_sqlx_fieldtext("N_Linea","text","fact","Numero de linea",false,"val",true));
		$micard->addComponent("2", 1, 2, $width, $height,new bas_sqlx_fieldtext("cantidad","text","fact","cantidad",true,"val",true));
		$micard->addComponent("1", 2, 1, $width, $height,new bas_sqlx_fieldtext("id_Cliente","text","fact","Cliente ",true,"val",true));
		$miobjeto=new bas_pdf_card();
		$miobjeto->loadcard($micard);
		$miobjeto->adjust(2,2);
		$miobjeto->beginDoc();
		$miobjeto->Onprint();
		$miobjeto->endDoc();
		
	}

	public function OnPdf(){
		switch($this->pdf_mode){
		case 'pdf1':
			$this->OnPdf1();//parrafo
			break;
		case 'pdf2': 
			$this->OnPdf2();//lista
			break;
		}
	}
	
	public function OnAction($action, $data){
		switch($action){
			case 'pdf1':
			case 'pdf2': $this->pdf_mode=$action; return array('pdf');
			case 'close': return array($action);
		}
	}
	
}

?>