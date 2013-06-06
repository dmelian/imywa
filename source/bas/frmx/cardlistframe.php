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
class bas_frmx_cardlistframe extends bas_frmx_listframe{
	public $jsClass= "bas_frmx_listframe";
	public $dataview;
	public $query;
	protected $cssComp;
	public $height;

	public $colComponents=array(); // los campos que compondrán los encabezado (en x) de la lista.
	public $rowComponents; // Contendran el valor pincipal por el cual se regirán los tipos de los distintos campos.
	public $mainComp;
	protected $mode;
	
	protected $autosize;
	public $fixedColums; 	
	
	public function __construct($id,$title='',$query="") {
		parent::__construct($id,$title);
		if ($query == "")$this->query = new bas_sqlx_querydef();
		else $this->query = $query;
// 		$this->tabs= $tabs;
		$this->mode = "edit";
		$this->fixedColums = 0;
	}
	
	public function getCssComponent(){
		return $this->cssComp;
	}
	
	public function createRecord(){
		$this->dataview = new bas_sqlx_dataview($this->query);
		$this->dataview->SetViewWidth(-1);
	}
	
	public function SetMode($mode="edit"){
		switch ($mode){
			case "edit":case "read": // ### en el caso del new, ¿tenemos que limpiar el contenido del current? (si)
				$this->mode = $mode;
			break;
			case "new":
				$this->mode = $mode;
// 				$this->dataview->current = array();$this->dataview->original = array();
			break;
			default:		
				global $_LOG;
				$_LOG->log("El modo insertado en FRMX_CardListFrame::SetMode es incorrecto");
		}
	}
	public function setFixed($nelem){
		$this->fixedColums = $nelem;
	}
	
	public function GetMode(){
		return $this->mode;		
	}
	

	
	public function setRecord($con=""){
		$this->dataview = new bas_sqlx_dataview($this->query);
		if ($con)$this->dataview->setConnection($con);
		$this->dataview->load_data();
		$this->dataview->SetViewWidth(-1);		
		$this->SetViewPos(0);		
	}
	public function initRecord(){
	  	$this->dataset = new bas_sqlx_dataview($this->query);		
		$this->dataset->initRecord();
		
	}
	
	public function SetViewPos($pos){
// 	    return $this->dataset->SetViewPos($pos);	setSelected
	    return $this->dataview->SetViewPos($pos);	

	}
	public function reloadData(){}
	
	public function Reload(){
		$this->dataview->query = $this->query;
		$this->dataview->load_data();
		$this->dataview->SetViewWidth(-1);		
		$this->SetViewPos(0);	
		
	}
	
	public function get_rows(){
		return $this->dataview->current; //Nota: ¿Que elementos pintamos? Tenemos que conocer cuantos caben en la ventana.
										// ### Plantearse
	}
	
	public function getComponentWidth($pos){
		return $this->colComponents[$pos]["width"];
	}
	
	public function uploadFile($id,$name){
		global $_SESSION;	
		$localDir = "/var/www/apps/upload/".$_SESSION->apps[$_SESSION->currentApp]->source."/docs/";
			// Insertamos el fichero en el servidor	
		if  ($_FILES[$id]["size"] < 20000)	{
			if ($_FILES[$id]["error"] > 0){
				return "Return Code: " . $_FILES[$id]["error"] . "<br />";
			}
			else{ // alamacenamos el fichelos en el directorio indicado.
				move_uploaded_file($_FILES[$id]["tmp_name"],
				$localDir . $id);			  //### TODO:sustituir el contaluz por la aplicacion actual   // "/var/www/apps/upload/contaluz/docs/"
			}
		}
		else{ return "Invalid file"; }
		return NULL;	
	}
	
	public function getComponent($pos){
		if ($this->query->existField($this->colComponents[$pos]["id"]))	return $this->query->getField($this->colComponents[$pos]["id"]);	
		
// 		if (isset($this->query->cols[$this->components[$pos]["id"]]))	return $this->query->cols[$this->components[$pos]["id"]];	
		return NULL;
	}
	
	
	public function getRowType($id){
		if (isset($this->rowComponents[$id]))	return $this->rowComponents[$id];	
		return NULL;
	}
	
	public function setAttr($id,$attr,$value){
// 	    if (isset($this->query->cols[$id])){
// 			$this->query->cols[$id]->setAttr($attr,$value);	
// 	    }
		if ($this->query->existField($id)){
			$this->query->getField($id)->setAttr($attr,$value);	
	    }
	    else{
			global $_LOG;
			$_LOG->log("Componente $id no asignado. FRMX_CardListFrame::setAttr");
	    }
	}
	
	public function setAttrRow($id,$attr,$value){
// 	    if (isset($this->query->cols[$id])){
// 			$this->query->cols[$id]->setAttr($attr,$value);	
// 	    }
        global $_LOG;
        $_LOG->log("Id a insertar $id, Atributo: $attr, Valor: $value");
		if ( isset($this->rowComponents[$id]) ){
			$this->rowComponents[$id]->setAttr($attr,$value);	
	    }
	    else{
			global $_LOG;
			$_LOG->log("Componente $id no asignado. FRMX_CardListFrame::setAttrRow");
	    }
	}

	public function addComponent($width=100,$temp="",$field_id=2,$visible=true){
		$this->addColComponent($width,$temp,$field_id,$visible);

	}	
	
	public function addColComponent($width=100,$temp="",$field_id=2,$visible=true){
// 		if (isset($this->colComponents[$field_id])){
// 			global $_LOG;
// 			$_LOG->log("FRMX::CardList. Ya se encuentra registrado el registro: $field_id");
// 		}
// 		else	$this->colComponents[$field_id] = array("width"=>$width,"id"=>$field_id,"visible"=>$visible);
		$this->colComponents[] = array("width"=>$width,"id"=>$field_id,"visible"=>$visible);

	}
	
	public function addRowComponent($field_id,$type="text"){
// 		if (isset($this->rowComponents[$field_id])){
// 			global $_LOG;
// 			$_LOG->log("FRMX::CardList. Ya se encuentra registrado el registro: $field_id");
// 		}
// 		else{
			switch ($type){

				case "textarea": case "text": case "enum": case "boolean": case "date": case "money": case "image": case "upload": case "number":
					$fieldtype = "bas_sqlx_field".$type;
					break;
					
				default:
					if (class_exists($type)) $fieldtype= $type;
					else {
						$_LOG->log("Tipo de datos inexistente: $type. FRMX_CardListFrame::addcol");
					}
					break;
			}
			$field = new $fieldtype($field_id,"");
			$this->rowComponents[$field_id]=$field;		
// 		}		
	}
	
		public function addCssComponent($id_field = "dynamicclass"){
		$this->cssComp = $id_field;
	}
	
	public function setHeight($height=18){
        $this->height = $height;
    }
	
	public function setMainCol($mainComp){
			$this->mainComp = $mainComp;
		if (isset($this->colComponents[$mainComp]))	$this->mainComp = $mainComp;
		else{
			global $_LOG;
			$_LOG->log("FRMX::CardList. No se encuentra registrado el registro: $mainComp");
		}	
	}
	
	public function saveData($data){
	  //unset($this->dataview->current);		
		if (isset($data)){
//			$this->dataview->current = [];
			foreach ($data as $field => $value){
				$this->dataview->current[$field] = $value;
			}
		}
	}
	
	public function OnPaintContent($page){
		$html = new bas_html_cardlistframe($this);
		if ($this->height)  $html->setHeight($this->height);
        if ($this->footer)  $html->setFooter($this->footer);

		$html->OnPaint($page);	
	}
	
	public function OnAction($action, $data){
	global $_LOG;
		switch($action){
			case "first": $this->dataview->first(); break;
			case "previous": $this->dataview->previous(); break;
			case "next": $this->dataview->next(); break;
			case "last": $this->dataview->last(); break;
			case "pdf":  $this->OnPdf(); break;
		}
	}
	
	public function get_Allrows(){
		return $this->dataview->Allrows();
	}
	
	public function Oncsv(){
		$csvcard=new bas_csv_card();
		$csvcard->loadcard($this);
		$csvcard->prepare();
		$csvcard->Onprint("card.csv");
	}
	
	function OnPdf($pdf){
		$pdflist= new bas_pdf_cardlistframe();
		$pdflist->OnPdf($pdf,$this);//aqui hay que introducir la base de datos y la tabla
	}
}

?>
