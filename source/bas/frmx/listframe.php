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
class bas_frmx_listframe extends bas_frmx_frame{

	public $jsClass= "bas_frmx_listframe";
	public $dataset;
	public $query;
	public $n_item;
	protected $cssComp;
	protected $autosize;
	protected $selector;
	protected $footer;
	public $height;
	
	public $fixedColums; // Número de columnas fijas dentro de components. Se tomarán los primeros X componentes.
	public $components = array();  // Vector con todos los campos que representarán las columnas de la lista
						  // el orden de este vector representará el orden visual final.

	public function __construct($id, $title="",$query=""){
		parent::__construct($id,$title);
		$this->fixedColums = 0;
		if ($query == "")$this->query = new bas_sqlx_querydef();
		else $this->query = $query;
		$this->n_item = 10;
		$this->cssComp = null;
		$this->selector = true;
		$this->height = 18;
	}
	public function setRecord($con=""){
		$this->dataset = new bas_sqlx_dataview($this->query);
		if ($con)$this->dataset->setConnection($con);
		$this->dataset->SetViewWidth($this->n_item);
		$this->dataset->load_data();
		$this->SetViewPos(0);		
	}
	
	public function setPivot($campo,$valor){
		$this->dataset->setPivot($campo,$valor);
	}
	
	public function Reload($paint=false,$con=""){
		$this->dataset->query = $this->query;
		if ($con != "")$this->dataset->setConnection($con);
		
		$this->dataset->load_data();
		$this->dataset->SetViewPos(0);
		$this->setSelected(-1);  // WARNING: Debemos mirar si tiene sentido hacerlo siempre. Desaparecera el seleccionado, útil en el borrado
		if ($paint)	$this->sendContent();
	}
	
	public function initRecord(){
	  	$this->dataset = new bas_sqlx_dataview($this->query);		
		$this->dataset->initRecord();
		
	}
	public function setHeight($height=18){
        $this->height = $height;
    }
	
	public function setMaxItem($nelem){
		$this->n_item = $nelem;
	}
	
	public function setFooter($value){
        $this->footer = $value;
    }
	
	public function getQuerySize(){
		if (isset($this->dataset)) return $this->dataset->getQuerySize(); else return null;
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
	
	public function autoSize(){
		$this->autosize=true;
	}
	
	public function showSelector($selector = true){
		$this->selector = $selector;
	}
	
// 	public function getComponent($pos){
// 		return $this->query->cols[$this->components[$pos]["id"]];	
// 	}

	public function getComponent($pos){
		if ($this->query->existField($this->components[$pos]["id"]))	return $this->query->getField($this->components[$pos]["id"]);	
		return NULL;
	}
	
	public function getComponentWidth($pos){
		return $this->components[$pos]["width"];
	}
	
	
	public function setFixed($nelem){
		$this->fixedColums = $nelem;
	}
	
	
// 	private function OnPrepareData(){
// 	}
	
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
// 	    return $this->dataset->SetViewPos($pos);	setSelected
	    return $this->dataset->SetViewPos($pos);	

	}
	
	public function createRecord(){
		$this->dataset = new bas_sqlx_dataview($this->query);
		$this->dataset->SetViewWidth($this->n_item);
	}
	
	public function setSelected($pos){
// 	    return $this->dataset->SetViewPos($pos);	setSelected
	    return $this->dataset->setSelected($pos-1); // Es necesario porque tratamos los datos desde el indice 0. Otra posibilidad es cambiar esta posición.	

	}
	public function getSelected(){
	    return $this->dataset->getSelected();
	}
	
	public function getkeySelected(){
		return $this->query->getautokeyRecord($this->getSelected());
	}
	
	public function existSelected(){
	    return $this->dataset->exitSelected();
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
		$html = new bas_html_listframe($this,$this->selector);
		if ($this->autosize) $html->autoSize();
		if (isset($this->footer)) $html->setFooter($this->footer);
		$html->OnPaint();
	}
	
	public function get_rows(){
		if (isset($this->dataset)) return $this->dataset->current; else return array(); 
		//Nota: ¿Que elementos pintamos? Tenemos que conocer cuantos caben en la ventana.
										// ### Plantearse
	}
	
	public function get_Allrows(){
		return $this->dataset->Allrows();
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
	
	/*protected function setFormatData($data){
		$content = array();
		$pos = 0;
		$cols = $this->query->cols;
		
		foreach($data as $row){
			foreach($row as $key => $value){
				$obj = $this->query->getField($key);
				if ($obj->type != "textarea"){
                    if (isset($obj)){
                        $content[$pos][$key] = $obj->OnFormat($value);		
                    }
				}
				else{
                    if (isset($obj)){
                        ob_start();
                            $obj->OnPaintList($value,"read"); 
                            $content[$pos][$key] = ob_get_contents();
                        ob_end_clean();
                    }
                    
				}
			}
			$pos++;
		}
		return $content;
	}*/
	
	
	protected function setFormatData($data){
        $content = array();
        $pos = 0;
        $cols = $this->query->getcols();
        
        foreach($data as $row){
            foreach($cols as $key => $obj){
//                 $obj = $this->query->getField($key);
                if ( isset($row[$key])) $value = $row[$key];
                else $value = "";
                
                if ($obj->type != "textarea"){
                    $content[$pos][$key] = $obj->OnFormat($value);      
                }
                else{
                    ob_start();
                        $obj->OnPaintList($value,"read"); 
                        $content[$pos][$key] = ob_get_contents();
                    ob_end_clean();
                }
            }
            $pos++;
        }
        return $content;
    }
	
	protected function sendContent($reset=false){
		$html = $this->get_rows();
		$nelem = count($html);
		if ($nelem == 0) $nelem=1;
		global $_LOG;
		$_LOG->debug("contenido a enviar", $html);
		$html = $this->setFormatData($html);
		
		$sel = $this->dataset->selectedPosRelative();
		$reset= ($this->getQuerySize()+1)*22;
// 		echo "{\"command\": \"reloadList\",\"frameid\":\"{$this->id}\",\"selected\": \"".$sel."\",\"size\": \"".$this->n_item."\",\"reset\": \"".$reset."\", \"data\": ".json_encode($html)."}";
		echo "{\"command\": \"reloadList\",\"frameid\":\"{$this->id}\",\"selected\": \"".$sel."\",\"size\": \"".$nelem."\",\"reset\": \"".$reset."\", \"data\": ".json_encode($html)."}";
		
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
	
	
}
?>
