<?php
class test_frmx_concepto_form_frame2 extends bas_frmx_cardlistframe{
    public function __construct($id, $title='') {
    
    
	parent::__construct($id,$title);
	
	$width=100;
	$height = 100;
	
	$this->query = new bas_sqlx_querydef();
	$this->query->add("concepto","test");
	$this->query->setkey(array('idconcepto'));
	$this->query->order= array("idconcepto"=>"asc");
	

	$this->query->addcol("idconcepto", "Concepto","concepto" ,true,'test');	
	$this->query->setfilter("D*",'idconcepto');
	$this->query->addcol("descripcion", "Descripción","concepto" ,false,'test',"upload");	
	
	$this->query->addcol("caption", "Caption","concepto" ,false,'test');
	$this->query->addcol("tipo", "Tipo:","concepto" ,false,'test',"enum");
	
	$this->addColComponent($width, $height, "idconcepto");
	$this->setMainCol("idconcepto");

	// 	$this->addColComponent($width, $height, "dynclass");	

	$this->addColComponent($width, $height, "descripcion");
	$this->addColComponent($width, $height, "caption");
	$this->addColComponent($width, $height, "tipo");

	$this->query->addcol("formato", "Tipo:","concepto" ,false,'test',"enum");	
	$this->query->addcol("opcionstring", "Opciones","concepto" ,false,'test');	
	$this->query->addcol("imagenstring", "Nombre de la imagen","concepto" ,false,'test');
	$this->query->addcol("datomultiple", "Dato multiple ","concepto" ,false,'test',null);


	$this->addColComponent($width, $height, "formato");
	$this->addColComponent($width, $height, "opcionstring");
	$this->addColComponent($width, $height, "imagenstring");
	$this->addColComponent($width, $height, "datomultiple");	
	
	$this->addRowComponent("DELTATOTFACT","number");
	$this->addRowComponent("DELTATOTFACTAUX","text");

	
// 	$qry = "select cluz.tarifaacceso.numperiodos from cluz.tarifaacceso, cluz.producto";
// 	$qry .=" where cluz.producto.producto='AHORA' AND cluz.producto.comercializadora='ENDESA' AND cluz.tarifaacceso.tarifaacceso= cluz.producto.tarifaacceso";
// 	
// 	"select cluz.concepto.idconcepto, cluz.concepto.formato ,cluz.concepto.formato from cluz.tptfacturacion, cluz.concepto where cluz.tptfacturacion.producto='AHORA' AND cluz.tptfacturacion.comercializadora='ENDESA' AND cluz.tptfacturacion.idconcepto= cluz.concepto.idconcepto";
// 	
	$qry = "select cluz.concepto.idconcepto, cluz.concepto.formato from cluz.concepto, cluz.linplantilla where cluz.linplantilla.idplantilla='CONSULT A' AND cluz.linplantilla.idconcepto = cluz.concepto.idconcepto";
						
	$ds = new bas_sql_myqrydataset($qry);
	
	$rec = $ds->reset();
	while ($rec){
		$rec = $ds->next();
	}
	$ds->close();
	

// 	$this->query->addcol("valor", "Valor","concepto" ,false,'test');	
// 	$this->query->addcol("calcorden", "Cálculo del orden","concepto" ,false,'test');	
// 	$this->query->addcol("distribuirvalor", "Valor del distibuidor","concepto" ,false,'test');
// 	$this->query->addcol("sumarizeoper", "aritmética","concepto" ,false,'test',"enum");
// 
// 
// 	$this->addComponent($width, $height, "valor");
// 	$this->addComponent($width, $height, "calcorden");
// 	$this->addComponent($width, $height, "distribuirvalor");
// 	$this->addComponent($width, $height, "sumarizeoper");	
// 	
// /*
//   fechavalor enum('fecha inicio periodo','fecha factura') DEFAULT NULL,
//   alerta int(11) NOT NULL DEFAULT '0',
//   alertimage int(11) NOT NULL DEFAULT '0',
//   alertmincount int(11) NOT NULL DEFAULT '1',
// 
// */
// 	$this->query->addcol("fechavalor", "Fecha","concepto" ,false,'test');	
// 	$this->query->addcol("alerta", "alerta","concepto" ,false,'test');	
// 	$this->query->addcol("alertimage", "alertimage","concepto" ,false,'test');
// 	$this->query->addcol("alertmincount", "alertmincount","concepto" ,false,'test',"enum");
// 
// 	$this->query->addcol("dynclass", "din","concepto" ,false,'test');	
// 	
// 	$this->addComponent($width, $height, "fechavalor");
// 	$this->addComponent($width, $height, "alerta");
// 	$this->addComponent($width, $height, "alertimage");
// 	$this->addComponent($width, $height, "alertmincount");	
// 	$this->addCssComponent("dynclass");
	$this->setRecord();
	
	
    }
}

	/*
  alertrepe int(11) DEFAULT NULL,
  dynclass varchar(20) DEFAULT NULL,
	*/
	?>
