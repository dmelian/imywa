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
class bas_frmx_cronoFrame extends bas_frmx_listframe{

	public $jsClass= "bas_frmx_listframe";
	public $dataset;
	
	public $periods=array();
	
	public $curPeriod;
	public $curDate;
	
    public $cronoHeader = array(); // se trata de un array simple->("prmero","segundo","tercero"), donde cada posicion representa el orden y valor del encabezado del cronograma propiamente dicho.

	public function __construct($id, $title="",$query=""){
		parent::__construct($id,$title);
		$this->fixedColums = 0;
		if ($query == "")$this->query = new bas_sqlx_cronoQuery();
		else $this->query = $query;
		$this->n_item = 10;
		$this->cssComp = null;
		$this->selector = true;
		
		$this->periods = array("day"=>"Diario","week"=>"Semanal","month"=>"Mensual","year"=>"Anual");
		$this->curPeriod = "year";
		
	}
	public function setRecord($con=""){
		$this->dataset = new bas_sqlx_cronoPointer($this->query);
		if ($con)$this->dataset->setConnection($con);
		$this->dataset->SetViewWidth($this->n_item);
		$this->cronoHeader = $this->dataset->load_data($this->curDate,$this->curPeriod);

		$this->SetViewPos(0);
	}
	
	public function initRecord(){
	  	$this->dataset = new bas_sqlx_cronoPointer($this->query);		
		$this->dataset->initRecord();
	}
	
	 public function getPeriods(){
        return $this->periods;
	 }
     public function periodSelected(){
        return $this->curPeriod;
     }
	
	public function addComponent($width=50, $height, $id_field){ 
		array_push($this->components,array("width"=>$width,"height"=>$height,"id"=>$id_field));
	}
	
	public function addCssComponent($id_field = "dynamicclass"){
		$this->cssComp = $id_field;
	}
	
	public function getCssComponent(){
		return $this->cssComp;
	}

	public function getComponent($pos){
		if ($this->query->existField($this->components[$pos]["id"]))	return $this->query->getField($this->components[$pos]["id"]);	
		return NULL;
	}
	
	public function getComponentWidth($pos){
		return $this->components[$pos]["width"];
	}
	
	public function setAttr($id,$attr,$value){
		if ($this->query->existField($id)){
			$this->query->getField($id)->setAttr($attr,$value);	
	    }
	    else{
			global $_LOG;
			$_LOG->log("Componente $id no asignado. FRMX_ListFrame::setAttr");
	    }
	}
	
	public function SetViewPos($pos){
	    return $this->dataset->SetViewPos($pos);	
	}
	
	public function Reload($paint=false){
		$this->dataset->query = $this->query;
		$this->cronoHeader = $this->dataset->load_data($this->curDate,$this->curPeriod);
		
		$this->dataset->SetViewPos(0);
		$this->setSelected(-1);  // WARNING: Debemos mirar si tiene sentido hacerlo siempre. Desaparecera el seleccionado, útil en el borrado
		if ($paint)	$this->sendContent();
	}
	
	public function createRecord(){
		$this->dataset = new bas_sqlx_cronoPointer($this->query);
		$this->dataset->SetViewWidth($this->n_item);
	}
	
	public function OnCommand($command, $data){
		switch($command){
			case 'gettabledef':
				echo "JSON:[\"setTableDef\",". json_encode($this->tabledef->export()). ']';
				break;
			
			case 'getdata':
				$this->tabledef->setposition($data['rowix'],$data['rowsxpage']);
				$ds= new bas_sql_myqrydataset($this->tabledef);
				echo "JSON:[\"setData\",". json_encode($ds->export()). ']';
				break;
		}
	}
	
	public function OnPaintContent(){
		$html = new bas_html_cronoFrame($this,$this->selector);
		if ($this->autosize) $html->autoSize();
		$html->OnPaint();
	}
	
	public function setDate($date){
        $this->curDate = $date;
//         $this->dataset->setDate($date);
    }
    
    public function setPeriod($period){
        $this->curPeriod = $period;
//         $this->dataset->setPeriod($period);
    }
	
	public function OnAction($action, $data){
		switch($action){
			case "first": $this->dataset->first(); break;
			case "previous": $this->dataset->previous(); break;
			case "next": $this->dataset->next(); break;
			case "last": $this->dataset->last(); break;
			case "ajax_previous": 
				$this->dataset->previous();
			break;
			case "scroll_move": 
			
			global $_LOG;
				if (isset($data["selected"])){
					$this->setSelected($data["selected"]);
					$_LOG->log("FRMX_LISTFRAME:: SE ha seleccionado el ". $data["selected"]);
				}
				$this->SetViewPos($data['pos']);
				$this->sendContent();
			return;// array('stay');
			case "ajax_next": $this->dataset->next(); $this->sendContent(); return array('stay');
				
		}
	}
	
	protected function setFormatData($data){
		$content = array();
		$pos = 0;
		foreach($data as $row){
			foreach($row as $key => $value){
				$obj = $this->query->getField($key);
				if (isset($obj)){
					$content[$pos][$key] = $obj->OnFormat($value);		
				}
			}
			$pos++;
		}
		return $content;
	}
	
	protected function sendContent($reset=false){
		$html = $this->get_rows();
		global $_LOG;
		$_LOG->debug("contenido a enviar", $html);
		$html = $this->setFormatData($html);
		
// 		global $_LOG;
// 		$_LOG->debug("contenido a enviar", $html);
// 		$_LOG->log("FRMX_LISTFRAME:: Valor resultante de la conversion:     ".addcslashes(json_encode($html),"\t\"\n\r") );
// 		$_LOG->log("FRMX_LISTFRAME:: la posicion seleccionada es ".$this->dataset->selectedPosRelative());
		$sel = $this->dataset->selectedPosRelative();
		$reset= ($this->getQuerySize()+1)*22;
		echo "{\"command\": \"reloadList\",\"frameid\":\"{$this->id}\",\"selected\": \"".$sel."\",\"size\": \"".$this->n_item."\",\"reset\": \"".$reset."\", \"data\": ".json_encode($html)."}";
	}
	
	function OnPdf($pdf){
		$pdflist= new bas_pdf_miclase();
		$pdflist->OnPdf($pdf,$this);//aqui hay que introducir la base de datos y la tabla
	}
	
	function Oncsv($csv){
		$csvlist=new bas_csv_listnew();
		$csvlist->loadlist($this);
		//$csvlist->prepare();
		$csvlist->Onprint($csv);
	}
	
	public function next(){
      $aux = $aux = explode('-',$this->curDate);
        global $_LOG;

      switch($this->curPeriod){
            case "day":
                $aux[1] = (($aux[1]+1)%13);
                $_LOG->log("NEXT:Antes del if ".$aux[1]);

                if ($aux[1] == 0) {
                    $aux[0]++;
                    $aux[1]++;
                }
                $_LOG->log("NEXT:DEspues del if ".$aux[1]);
            break;
            case "week": case "month":
                $aux[0]++;
            break;
            case "year":
//                 $aux = explode('-',$this->curDate);
            break;
        
       }
       $this->setDate("$aux[0]-$aux[1]-$aux[2]");
	}
	
	
	public function previous(){
        $aux = $aux = explode('-',$this->curDate);
        global $_LOG;
        switch($this->curPeriod){
              case "day":
                  $aux[1] = ($aux[1]-1)%13;
                  $_LOG->log("Antes del if ".$aux[1]);
                  if ($aux[1] == 0) {
                      $aux[1] = 12; $aux[0]--;
                  }
                  $_LOG->log("DEspues del if ".$aux[1]);
              break;
              case "week": case "month":
                  $aux[0]--;
              break;
              case "year":
  //                 $aux = explode('-',$this->curDate);
              break;
          
        }
        $this->setDate("$aux[0]-$aux[1]-$aux[2]");
	}
	
}
?>
