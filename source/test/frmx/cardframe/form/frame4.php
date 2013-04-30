<?php
class test_frmx_cardframe_form_frame4 extends bas_frmx_cardframe{
	
    public function __construct($id, $tabs='', $grid=array('width'=>5,'height'=>5)) {
		parent::__construct($id,$tabs,$grid);
				
		$this->tabs = array("Primera solapa","Segunda solapa");
		
		$this->addComponent("Primera solapa", 1, 1, 1, 1, new bas_sqlx_fieldtext("N_Factura","text","fact","Numero de Factura",false,"val",true));
		$this->addComponent("Primera solapa", 2, 1, 3, 1, new bas_sqlx_fieldtext("N_Linea","text","fact","Numero de linea",false,"val",true));
		$this->addComponent("Primera solapa", 1, 2, 2, 2,new bas_sqlx_fieldtext("cantidad","text","fact","cantidad",true,"val",true));
		$this->addComponent("Primera solapa", 3, 2, 1, 1,new bas_sqlx_fieldtext("id_Cliente","text","fact","Cliente ",true,"val",true));

    }
}