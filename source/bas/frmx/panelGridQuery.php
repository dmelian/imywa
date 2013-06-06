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
class bas_frmx_panelGridQuery extends bas_frmx_panelGrid {
    
    public $record;
	public $query;
	protected $posIni;
	protected $posFin;
	public function __construct($id,$grid="",$query="") {
        parent::__construct($id,$grid);
        if (!$query) $query = new bas_sqlx_querydef();
        $this->query = $query;
        $this->posFin = $this->posIni = 1;
    }
	
	public function setRecord(){
	  	$this->record = new bas_sqlx_dataview($this->query);	
	  	$this->record->SetViewWidth($this->numItems()*100);
//         $this->record->SetViewWidth(-1));
	  	
		$this->record->load_data();
		$this->record->first();
		$this->createGrid();
	}
	
	public function initRecord(){
	  	$this->record = new bas_sqlx_dataview($this->query);	
	  	$this->record->SetViewWidth($this->numItems()*100);
//         $this->record->SetViewWidth(-1);
	  	
		$this->record->initRecord();
		
	}
	public function createRecord(){
		$this->record = new bas_sqlx_dataview($this->query);
		$this->record->SetViewWidth($this->numItems()*100);
//         $this->record->SetViewWidth(-1);
		
// 		$this->record->SetViewWidth($this->n_item);
	}	
	
	public function reloadData(){}
	
	public function Reload(){
		$this->record->query = $this->query;
		$this->record->load_data();
		$this->record->first();
		$this->createGrid();
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
	
// 	public function getComponent($y,$x){
// 		if (isset($this->components[$y][$x]))	return $this->components[$y][$x]["id"];	
// 		return NULL;
// 	}
	
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

 
    
    protected function createGrid(){
        unset($this->components);
        $this->component = array();
        $data = $this->record->current;
        global $_LOG;
//         $_LOG->debug("valor del current: ",$data);
        $nelem = count($data);
        if ($this->posIni == 1) {
            $ajuste = 0;
            $aux =1;
        }
        else {
            $ajuste = 1;
            $aux=0;
            $this->addComponent(1,1,"#","#","prevGrid");
        }
        if ($nelem >= ($this->posIni+$this->numItems()-1)) $ultimo = 1;
        else $ultimo = 0;
        
        $_LOG->log("Numero de elementos: ".$nelem);
        
        $ind = 0;
        for($y=0;$y < $this->grid["height"]; $y++){
            for($x=0;$x < $this->grid["width"]; $x++){
                if ($aux == 1 ) {
                    $sig = $this->posIni + (($y*$this->grid["width"]) +($x-$ajuste)) -1;
                    if (! isset($data[$sig])) break;
                    if (($ultimo== 1) and ( (($y*$this->grid["width"]) +$x) == ($this->numItems()-1)))$this->addComponent($this->grid["height"],$this->grid["width"],"#","#","nextGrid");
                    else  {
                        $this->addComponent($y+1,$x+1,$data[$sig][$this->classMain],$data[$sig][$this->classMain]);
                        $ind++;
                    }
                    
                }
                else
                    $aux=1;
            }
        }
        $this->posFin = $this->posIni +$ind;
//         $this->previousView();
//         $_LOG->log("Valor anterior al actual!!". $this->posIni);
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
			case "nextGrid": $this->nextView(); break; //
			case "prevGrid": $this->previousView(); break;
			case "pdf":  $this->OnPdf(); break;
		}
	}
	
	
	protected function nextView(){
        global $_LOG;
        $this->posIni = $this->posFin;	
        $_LOG->log("######### nextView: INI: ".$this->posIni);
        $this->createGrid();
	}
	
	protected function previousView(){
        $anterior = $this->posIni-1;
        $desplazamiento = ($anterior % $this->numItems()) +1;
        $this->posIni -= $desplazamiento ;   
        if ($this->posIni == 0) $this->posIni = 1;
        
        global $_LOG;
        $_LOG->log("######### previousView: INI: ".$this->posIni);
        
        $this->createGrid();
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
