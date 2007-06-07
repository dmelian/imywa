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
	
	public $strict=false;
	public $jsClass= "bas_frmx_listframe";
	public $dataset;
	public $query;
	public $n_item;
	protected $cssComp;
	protected $autosize;
	protected $selector;
	protected $footer;
	public $height;
	public $dbClick;
	
	
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
		$this->dbClick = false;		
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
	
	public function setDbClick($value=true){
		$this->dbClick = $value;
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
	
	public function addComponent($width=50, $height, $id_field, $strict=null){
		#strict- The field must exists, else the component doesn't insert and a log is created. 
		global $_LOG;
		
		if (is_null($strict)) $strict= $this->strict;
		$exists= $this->query->existField($id_field);
		 
		if ($strict && !$exists) { 
			$_LOG->log("FRMX: Unknown id for the compoment $id_field. FrameId: {$this->id}", 5, 'frameList');
		} else {
			array_push($this->components, array(
				"width" => $width
				, "height" => $height
				, "id" => $id_field
				, 'select-order' => count($this->components)
				, 'visible' => $exists ? $this->query->getField($id_field)->visible : 'true'
			));
		}
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
	
	public function getComponent($pos){
		if ($this->query->existField($this->components[$pos]["id"])) {	
			return $this->query->getField($this->components[$pos]["id"]);	
		} else return NULL;
	}
	
	public function getComponentPos($id){
		for($pos=0; $pos < count($this->components); $pos++){
			if ($this->components[$pos]['id'] == $id) return $pos;
		}
		return -1;
	}
	
	public function getComponentWidth($pos){
		return $this->components[$pos]["width"];
	}
	
	
	public function setFixed($nelem){
		$this->fixedColums = $nelem;
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

	public function loadConfig(){
		if (!$config= $this->loadConfigFile()) return;
		$aux = array();
		for($ix=0; $ix<count($this->components); $ix++){
			if (isset($config[$this->components[$ix]['id']])){
				foreach($config[$this->components[$ix]['id']] as $property => $value) {
					$this->components[$ix][$property]= $value;
				}
				$aux[$this->components[$ix]['select-order']]= $this->components[$ix];
			}
		}
		global $_LOG;
		$_LOG->debug("Componeentes",$this->components);
		unset($this->components);
		$this->components = $aux;
		$_LOG->debug("Componeentes Luego",$this->components);
	}
	
	public function saveConfig(){
		# Save all configurable attributes in a config file.
		$configurableProps= explode(',','width,select-order,visible');
		$config= array();
		foreach($this->components as $component){
			$config[$component['id']]= array();
			foreach($configurableProps as $property) $config[$component['id']][$property]= $component[$property];
		}
		$this->saveConfigFile($config);
	}
	
	public function setChangeConfig($field,$property, $value){
		foreach($this->components as &$component){
			if ($component["id"] == $field){ 
				$component[$property] = $value;
			}
		}
	}
	
	public function setOrderComponents($order){
		$order = explode(',',$order);
		$pos =0;
				global $_LOG;

		foreach($order as $field){
			$this->setChangeConfig($field,'select-order',$pos);
			$pos++;
		}
		
		foreach($this->components as $component){
				$_LOG->log("HAcemos el valor--- ".$component["id"].".".$component["select-order"]);
		}
		$this->saveConfig();
	}
	
	public function SetViewPos($pos){
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
				if (isset($data["selected"])){
					$this->setSelected($data["selected"]);
				}
				$this->SetViewPos($data['pos']);
				$this->sendContent();
				return;
				
			case "ajax_next": $this->dataset->next(); $this->sendContent(); return array('stay');
			
			case 'setColWidth':
				if (($pos=$this->getComponentPos($data['field'])) >= 0) {
					$this->components[$pos]['width']= $data['width'];
					$this->saveConfig();
				} // ELSE LOG INVALID COMPONENT.
				break;
			case 'setColOrder':
				$this->setOrderComponents($data["order"]);
				break;
		}
	}

	protected function setFormatData($data){
		$content = array();
		$pos = 0;
		$cols = $this->query->getcols();

		foreach($data as $row){
			foreach($cols as $key => $obj){
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
		if ($nelem < $this->n_item) $html[] = array();
		$html = $this->setFormatData($html);
		
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
		$csvlist->Onprint($csv);
	}
	
	
}
?>
