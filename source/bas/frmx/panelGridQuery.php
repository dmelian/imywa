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
	
	public $mainField; // query field id for the main caption. 
	public $topField; // query field id for the top caption.
	public $bottomField; // query field id for the bottom caption. 
	
	
	
	public function __construct($id,$grid="") {
        parent::__construct($id,$grid);
        $this->posFin = $this->posIni = 1;
        $this->type = "gridQuery";
    }
	
	
	public function setQuery($query){
		$this->query = $query;
		$this->buildRecord();
	}
	
	private function buildRecord(){
	  	$this->record = new bas_sqlx_dataview($this->query);	
	  	$this->record->SetViewWidth($this->numItems()*100);
//         $this->record->SetViewWidth(-1);
	  	
		$this->record->load_data();
		$this->record->first();
		$this->createGrid();
	}
	/*
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
	
	public function Reload(){
		$this->record->query = $this->query;
		$this->record->load_data();
		$this->record->first();
		$this->createGrid();
	}
	*/

    protected function createGrid(){  // ### TODO: improve variable names
        unset($this->components);
        $this->components = array();
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
            $this->addComponent(1,1,"#","#","prevGrid","","empty");
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
                    if (($ultimo== 1) and ( (($y*$this->grid["width"]) +$x) == ($this->numItems()-1)))$this->addComponent($this->grid["height"],$this->grid["width"],"#","#","nextGrid","","empty");
                    else  {
                        $this->addComponent($y+1,$x+1,$data[$sig][$this->mainField],$data[$sig][$this->mainField]);
                        $ind++;
                    }
                }
                else
                    $aux=1;
            }
        }
        $this->posFin = $this->posIni +$ind;
    }
	
	
	public function OnAction($action, $data){
	
		switch($action){
			case "nextGrid": $this->nextView(); break; 
			case "prevGrid": $this->previousView(); break;
			case "pdf":   $this->OnPdf(); break;
			case "csv":  $this->OnCsv(); break;
		}
	}
	
	
	protected function nextView(){
        $this->posIni = $this->posFin;	
		global $_LOG;
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
	
	
}

?>
