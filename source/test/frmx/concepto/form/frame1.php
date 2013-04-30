<?php
class test_frmx_concepto_form_frame1 extends bas_frmx_cardframe{
    public function __construct($id, $tabs='', $grid=array('width'=>4,'height'=>4)) {
    
    
	parent::__construct($id,$tabs,$grid);
	
	//addComponent($tab, $x, $y, $width, $height, $field);
	//construct FIELD ($id,$type,$name, $caption, $editable, $value,$visible). ##Nota: el campo type deberia quitarse.
	$width=2;
	$height = 1;
	
	$this->SetMode("edit");
	
	$this->tabs = array("Primera","Segunda","Tercera","Cuarta");
	
	
	$this->query = new bas_sqlx_querydef();
	$this->query->add("concepto","test");
	$this->query->setkey(array('idconcepto'));
	$this->query->order= array("idconcepto"=>"asc");
	
	/*
      idconcepto varchar(20) NOT NULL,
      descripcion varchar(200) DEFAULT NULL,
      caption varchar(30) NOT NULL,  ## Nota: ¿Para que se usa este campo?
      tipo enum('editable','fijo','calculado') NOT NULL DEFAULT 'editable',
	*/
	$this->query->addcol("idconcepto", "Concepto","concepto" ,true,'test');	
	$this->query->addcol("descripcion", "Descripción","concepto" ,false,'test',"upload");	
	$this->query->addcol("caption", "Caption","concepto" ,false,'test');
	$this->query->addcol("tipo", "Tipo:","concepto" ,false,'test',"enum");
	$this->setAttr("tipo","enum",array('editable'=>'editable','fijo'=>'fijo','calculado'=>'calculado'));
	$this->setAttr('idconcepto','lookup','frmx_cardframe_form');
	
	$this->addComponent("Primera", 1, 1, $width, $height,"idconcepto",'test');
	$this->addComponent("Primera", 1, 2, $width, $height, "descripcion",'test');
	$this->addComponent("Primera", 1, 3, $width, $height,"caption",'test');
	$this->addComponent("Primera", 1, 4, $width, $height,"tipo",'test');
/*
  formato enum('numérico','moneda','booleano','opción') NOT NULL DEFAULT 'numérico',
  opcionstring varchar(250) DEFAULT NULL,
  imagenstring varchar(80) DEFAULT NULL,
  datomultiple int(11) NOT NULL DEFAULT '0',

*/
	$this->query->addcol("formato", "Tipo:","concepto" ,false,'test',"enum");	
	$this->query->addcol("opcionstring", "Opciones","concepto" ,false,'test');	
	$this->query->addcol("imagenstring", "Nombre de la imagen","concepto" ,false,'test');
	$this->query->addcol("datomultiple", "Dato multiple ","concepto" ,false,'test');
	$this->setAttr("formato","enum",array('numérico'=>'numérico','moneda'=>'moneda','booleano'=>'booleano','opción'=>'opción'));


	$this->addComponent("Segunda", 2, 1, $width, $height,"formato");
	$this->addComponent("Segunda", 2, 2, $width, $height,"opcionstring");
	$this->addComponent("Segunda", 3, 3, $width, $height,"imagenstring");
	$this->addComponent("Segunda", 3, 4, $width, $height,"datomultiple");
/*
  valor double DEFAULT NULL,
  calcorden int(11) DEFAULT NULL,
  distribuirvalor int(11) DEFAULT NULL,
  sumarizeoper enum('media','suma','mínimo','máximo','moda') DEFAULT NULL,
*/

	$this->query->addcol("valor", "Valor","concepto" ,false,'test');	
	$this->query->addcol("calcorden", "Cálculo del orden","concepto" ,false,'test');	
	$this->query->addcol("distribuirvalor", "Valor del distibuidor","concepto" ,false,'test');
	$this->query->addcol("sumarizeoper", "aritmética","concepto" ,false,'test',"enum");
	$this->setAttr("sumarizeoper","enum",array('media'=>'media','suma'=>'suma','mínimo'=>'mínimo','máximo'=>'máximo','moda'=>'moda'));

	
	$this->addComponent("Tercera", 1, 1, 1, $height, "valor");
	$this->addComponent("Tercera", 1, 2, $width, $height,"calcorden");
	$this->addComponent("Tercera", 3, 1, $width, $height,"distribuirvalor");
	$this->addComponent("Tercera", 3, 2, $width, $height,"sumarizeoper");
/*
  fechavalor enum('fecha inicio periodo','fecha factura') DEFAULT NULL,
  alerta int(11) NOT NULL DEFAULT '0',
  alertimage int(11) NOT NULL DEFAULT '0',
  alertmincount int(11) NOT NULL DEFAULT '1',

*/
	$this->query->addcol("fechavalor", "Fecha","concepto" ,false,'test');	
	$this->query->addcol("alerta", "alerta","concepto" ,false,'test');	
	$this->query->addcol("alertimage", "alertimage","concepto" ,false,'test');
	$this->query->addcol("alertmincount", "alertmincount","concepto" ,false,'test',"enum");
	$this->setAttr("alertmincount","enum",array('fecha inicio periodo'=>'fecha inicio periodo','fecha factura'=>'fecha factura'));

	$this->addComponent("Cuarta", 1, 1, 1, $height, "fechavalor");
	$this->addComponent("Cuarta", 2, 1, $width, $height, "alerta");
	$this->addComponent("Cuarta", 3, 1, $width, $height,"alertimage");
	$this->addComponent("Cuarta", 1, 2, $width, $height,"alertmincount");
	
	$this->setRecord();
	
	
    }
}

	/*
  alertrepe int(11) DEFAULT NULL,
  dynclass varchar(20) DEFAULT NULL,
	*/
?>

