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
class bas_frmx_panelGrid {
	public $jsClass= "bas_frmx_cardframe";
	public $record;
	public $query;
	public $labelwidth;
	
	public $grid;
	public $components=array();
	
	public $classMain;
	public $classSuper;
	public $classSub;
	public $id;
	
	protected $mode;
	public $type;
// 	protected $actions=array();
	
	public function __construct($id,$grid=array('width'=>4,'height'=>5)) {
		$this->id = $id;
		$this->grid= array('width'=>4,'height'=>5);
		$this->labelwidth = 30;
		$this->mode = "edit";
		$this->type = "normal";
	}
	
	public function SetMode($mode="edit"){
		switch ($mode){
			case "edit":case "read": // ### en el caso del new, ¿tenemos que limpiar el contenido del current? (si)
				$this->mode = $mode;
			break;
			case "new":	
				$this->mode = $mode;
				$this->record->original = $this->record->current = array();
			break;
			default:		
				global $_LOG;
				$_LOG->log("El modo insertado en FRMX_CardFrame::SetMode es incorrecto");
		}
	}
	
	public function GetMode(){
		return $this->mode;		
	}
	
	public function setRecord(){
	  	$this->record = new bas_sqlx_record($this->query);		
		$this->record->load_data();
		$this->record->first();
	}
	
	public function initRecord(){
	  	$this->record = new bas_sqlx_record($this->query);		
		$this->record->initRecord();
		
	}
	public function createRecord(){
		$this->record = new bas_sqlx_record($this->query);
// 		$this->record->SetViewWidth($this->n_item);
	}	
	
	public function reloadData(){}
	
	public function Reload(){
		$this->record->query = $this->query;
		$this->record->load_data();
		$this->record->first();
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
	
	public function setLabelWidth($width){
		$this->labelwidth = $width;
	}
	
	public function getComponent($y,$x){
		if (isset($this->components[$y][$x]))	return $this->components[$y][$x]["id"];	
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
			$_LOG->log("Componente $id no asignado. FRMX_CardFrame::setAttr");
	    }
	}

	public function OnPaint(){
		$html = new bas_html_panelGrid($this);
		$html->OnPaint();	
	}
	
	public function addComponent($y=0, $x=0, $field_id,$paintCaption=true){
		$this->components[$y][$x] = array("x"=>$x,"y"=>$y,"id"=>$field_id,"caption"=>$paintCaption);
	}
	
	
	public function delComponent($id){
        foreach($this->components as $item => $component){
            if ($component['id'] == $id)  unset($this->components[$item]);
        }
	}
	
	public function saveData($data){
	  //unset($this->record->current);
		
		if (isset($data)){
//			$this->record->current = [];
			foreach ($data as $field => $value){
				$this->record->current[$field] = $value;
			}
		}
	}
	
	public function selecttab($tab){// marcamos como seleccionado el tab indicado
		
	}
	

	
	public function OnAction($action, $data){
	global $_LOG;
		switch($action){
			case "first": $this->record->first(); break;
			case "previous": $this->record->previous(); break;
			case "next": $this->record->next(); break;
			case "last": $this->record->last(); break;
			case "pdf":  $this->OnPdf(); break;
		}
	}
	
	public function Oncsv($csv){
		$csvcard=new bas_csv_card();
		$csvcard->loadcard($this);
		//$csvcard->prepare();
		$csvcard->Onprint($csv);
	}
	
	public function OnPdf($pdf){
		$pdfcard=new bas_pdf_card();
		$pdfcard->OnPdf($pdf,$this);
		
	}
}

?>
