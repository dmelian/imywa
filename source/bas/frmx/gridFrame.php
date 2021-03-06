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
class bas_frmx_gridFrame extends bas_frmx_frame{
	public $jsClass= "bas_frmx_cardframe";
	public $record;
	public $query;
	public $labelwidth;
	
	public $tabs;// Sugerencia: vector asociativo con clave como nombre y valor true o false (indicando si está activo o no)
	public $grid;
	public $components=array();
	
	public $header;
	
	protected $mode; // ###: ABORRAR
// 	protected $actions=array();
	
	public function __construct($id,$tabs,$grid="") {
		parent::__construct($id);
		if ($grid=="") $grid=array('width'=>4,'height'=>5);
		$this->grid= $grid;
		$this->tabs= $tabs;
		$this->labelwidth = 30;
		$this->mode = "edit";
		
		$this->header= "";
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
	
	public function setHeader($header=""){
		$this->header = $header;
	}
	
/*	public function getObjComponent($id){
        for($y=1;$y <= $this->grid["height"]; $y++){
            for($x=1;$x <= $this->grid["width"]; $x++){
                if(isset($this->components[$y][$x])){
                    $componente = $this->components[$y][$x];
                    if ($id == $componente["id"]){
						$aux = &$this->components[$y][$x]["obj"];
						return $aux;
                    }
                }
                
            }
        }
        return null;
	}*/

	public function getObjComponent($id){
        $component = $this->getComponent($id);
        if (isset($component)) {
			$aux = &$component["obj"];
			return $aux;
        }
        else return null;
	}
	
	private function getComponent($id){
		for($y=1;$y <= $this->grid["height"]; $y++){
            for($x=1;$x <= $this->grid["width"]; $x++){
                if(isset($this->components[$y][$x])){
                    if ($id == $this->components[$y][$x]["id"]){
						$aux = &$this->components[$y][$x];
						return $aux;
                    }
                }
                
            }
        }
        return null;
	}
	
	public function setAttr($grid,$id,$attr,$value){
		$objGrid = $this->getObjComponent($grid);
		if (isset($objGrid)) return $objGrid->setAttr($id,$attr,$value);
	    else{
			global $_LOG;
			$_LOG->log("Grid  $grid inexistente. ".get_class($this).":SetAttr");
			return false;
	    }
	}

	public function OnPaint($page){
		$html = new bas_html_gridFrame($this);
		$html->OnPaint($page);	
	}
	
	public function addComponent( $field_id,$obj,$y=0, $x=0,$width=1,$height=1){
          $this->components[$y][$x] = array("id"=>$field_id,"obj"=>$obj,"width"=>$width,"height"=>$height);
	}
	
	
	public function delComponent($id){ //no funciona ahorra components es una array de arrays
        $component = $this->getComponent($id);
        if (isset($component)){
			unset($component); // ###! a testear.
			return true;
        }
        else{ 
			global $_LOG;
			$_LOG->log("No existe el componente $id. ".get_class($this)."::delComponent");
			return false;
        }
	}
	
	public function OnAction($action, $data){
	global $_LOG;
		switch($action){
			case 'nextGrid': case "prevGrid": $this->getObjComponent($data["idPanel"])->OnAction($action,$data); break;
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

/*
###################################################################################################
#######		funciones utilizadas para la organización de los componentes ordenados.  ##########
###################################################################################################
*/
	private function initGrid(){
	   // $table = array();
	    for($row = 1; $row<=$this->grid["height"];$row++){
		for($colom = 1; $colom<=$this->grid["width"];$colom++){
		    $table[$row][$colom]= -2;
		}
	    }
	    return $table;
	}
	private function conflictPos($tab,$x,$y,$id){
		global $_LOG;
		$_LOG->log("Existe solapamiento en el tab:$tab. En la fila $y y columna $x, con el component $id. FRMX_CardFrame::sortComponents");
	
	
	}
	public function sortComponents(){
	    $grid = $this->initGrid();
	    $nelem = count($this->tabs);
	    $sort=array();
	    $componets_temp = $this->components;
	    
	    for ($index = 0; $index<$nelem;$index++){
			$tabCurrent = $this->tabs[$index];
			$sort[$tabCurrent] = $grid;
			$ncomp = count($this->components);
			
			for($indComp=0;$indComp<$ncomp;$indComp++){
				if ($tabCurrent==$this->components[$indComp]["tab"]){
					$x = $this->components[$indComp]["x"];
					$y = $this->components[$indComp]["y"];
					if ($sort[$tabCurrent][$y][$x] != -2) $this->conflictPos($tabCurrent,$x,$y,$this->components[$indComp]["id"]);
					$sort[$tabCurrent][$y][$x] = $indComp;
					$size =  $this->components[$indComp]["width"];
					
					for ($indice = 1; $indice<$size;$indice++){
						if ($sort[$tabCurrent][$y][$x+$indice] != -2) $this->conflictPos($tabCurrent,$x,$y,$this->components[$indComp]["id"]);
						$sort[$tabCurrent][$y][$x+$indice] = -1;
					}
					// ¿ que pasa con la altura ? no existen componentes de mas de 1 fila.?
				}
			}	
	    }
	    return $sort;	
	}
	
}

?>
