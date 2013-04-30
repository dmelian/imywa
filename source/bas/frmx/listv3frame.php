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
class bas_frmx_listv3frame extends bas_frmx_frame{
/* El original modificado con la base del bas_htm_form2 para que saque los menus */	

/*****************************************************************************************************************/	
/* VARIABLES DEL BAS_FRMX_LISTFRAME */
/*****************************************************************************************************************/	
	public $jsClass= "bas_frmx_listframe";
	public $dataset;
	public $query;
	public $n_item;
	protected $cssComp;
	protected $autosize;
	private $selector;
	
	public $fixedColums; // Número de columnas fijas dentro de components. Se tomarán los primeros X componentes.
	public $components = array();  // Vector con todos los campos que representarán las columnas de la lista
						  // el orden de este vector representará el orden visual final.
	
	
/*****************************************************************************************************************/	
/* VARIABLES DEL BAS_FRM_LIST */	
/*****************************************************************************************************************/	
	//public $id; se hereda del bas_frmx_frame
	protected $tbdef_V3;
	protected $toolbar_V3=array();
	protected $header_V3;
	protected $subforms_V3=array();
	protected $footer_V3;
	protected $menu_V3;
	private $list_V3;
	
/*****************************************************************************************************************/	
/* FUNCIONES DEL BAS_FRMX_LISTFRAME */
/*****************************************************************************************************************/	
	public function __construct($id, $title="",$query=""){
		parent::__construct($id,$title);
		$this->fixedColums = 0;
		if ($query == "")$this->query = new bas_sqlx_querydef();
		else $this->query = $query;
		$this->n_item = 10;
		$this->cssComp = null;
		$this->selector = true;
		/* ----------------  V3 CODE ---------------- */		
		$this->header_V3 = new bas_htm_elements(); 
		$this->footer_V3 = new bas_htm_elements();
		
	}
	
	
	public function setRecord($con=""){
		$this->dataset = new bas_sqlx_dataview($this->query);
		if ($con)$this->dataset->setConnection($con);
		$this->dataset->SetViewWidth($this->n_item);
		$this->dataset->load_data();
		$this->SetViewPos(0);		
	}
	
	public function setMaxItem($nelem){
		$this->n_item = $nelem;
	}
	
	public function getQuerySize(){
		return $this->dataset->getQuerySize();
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
	
	
	private function OnPrepareData(){
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
// 	    return $this->dataset->SetViewPos($pos);	setSelected
	    return $this->dataset->SetViewPos($pos);	

	}
	
	public function Reload($paint=false){
		$this->dataset->query = $this->query;
		$this->dataset->load_data();
		$this->dataset->SetViewPos(0);
		if ($paint)	$this->sendContent();
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
		$this->OnPaint_V3();
/*
		$html = new bas_html_listframe($this,$this->selector);
		if ($this->autosize) $html->autoSize();
		$html->OnPaint();
*/
		}
	
	public function get_rows(){
		return $this->dataset->current; //Nota: ¿Que elementos pintamos? Tenemos que conocer cuantos caben en la ventana.
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
	
// 	private function sendContent(){
// 		ob_start();
// 			$htmlObj = new bas_html_listframe($this);
// 			$htmlObj->OnPaintList();
// 		$html= ob_get_contents();
// 		ob_end_clean();
// 		echo "{\"command\": \"reload\",\"frameid\":\"{$this->id}\", \"selector\": \".ia_list_container\",\"content\": \"".addcslashes($html,"\t\"\n\r")."\"}";
// 	}

	private function sendContent($reset=false){
		$html = $this->get_rows();
		
		global $_LOG;
// 		$_LOG->log("FRMX_LISTFRAME:: Valor resultante de la conversion:     ".addcslashes(json_encode($html),"\t\"\n\r") );
		$_LOG->log("FRMX_LISTFRAME:: la posicion seleccionada es ".$this->dataset->selectedPosRelative());
		$sel = $this->dataset->selectedPosRelative();
		echo "{\"command\": \"reloadList\",\"frameid\":\"{$this->id}\",\"selected\": \"".$sel."\",\"size\": \"".$this->n_item."\",\"reset\": \"".$reset."\", \"data\": ".json_encode($html)."}";
	}
	
	function OnPdf($temp=""){
		$pdflist= new bas_pdf_miclase();
		$pdflist->OnPdf($this);//aqui hay que introducir la base de datos y la tabla
	}
	
	function Oncsv(){
		$csvlist=new bas_csv_listnew();
		$csvlist->loadlist($this);
		$csvlist->prepare();
		$csvlist->Onprint("list.csv");
	}
	
	
/*****************************************************************************************************************/	
/* FUNCIONES DEL BAS_FRM_LIST */	
/*****************************************************************************************************************/	

	public function addsubform_V3($frm){
		$this->subforms[$frm->id] = $frm;
	}	

/*
			$this->toolbar[] = array('id'=>"{$id}primero", 'image'=>'primero', 'description'=>'Ir al primero.');
			$this->toolbar[] = array('id'=>"{$id}anterior", 'image'=>'anterior', 'description'=>'Ir al anterior.');
			$this->toolbar[] = array('id'=>"{$id}siguiente", 'image'=>'siguiente', 'description'=>'Ir al siguiente.');
			$this->toolbar[] = array('id'=>"{$id}ultimo", 'image'=>'ultimo', 'description'=>'Ir al último.');
			$this->toolbar[] = array('id'=>"{$id}mostrar", 'image'=>'mostrar', 'description'=>'Seleccionar las columnas del listado.');
			$this->toolbar[] = array('id'=>"{$id}filtros", 'image'=>'filtros', 'description'=>'Aplicar diferentes filtros por columnas.');
			$this->toolbar[] = array('id'=>"{$id}orden", 'image'=>'orden', 'description'=>'Ordenar el listados por alguna de sus columnas.');
 */	
	
	public function OnLoad_V3(){
		foreach($this->subforms as $frm) if (method_exists($frm, 'OnLoad')) $frm->OnLoad();
	}
	
	
	public function OnPaint_V3(){
		if (method_exists($this->tbdef_V3, 'refresh')) $this->tbdef_V3->refresh();
		
		$frm = new bas_htm_elements();
		
		$frm->opendiv('list_head');
		// Cabecera de filtros automáticos
		$cols = $this->tbdef_V3->getcolsbyproperty('keyfiltered',true);
		$countcols = count($cols);
		if ($countcols){
			$frm->opendiv('autofilters');
			$fdef = new bas_dat_tabledef();
			$i=0;
			foreach($cols as $col){
				$i++;
				$fdef->addcol($col['caption'],$col['id']); 
				if (isset($col['lookup'])) $fdef->setproperty('lookup',$col['lookup']);
				elseif ($i == $countcols) $fdef->setproperty('lookup');
				$fdef->settemplate('hidden');
			}
			$autofilter = $this->tbdef->getautokeyfilter();
			$card = new bas_htm_card($fdef, $autofilter);
			$frm->add($card);
			$frm->closediv();
		}
		// Cabecera del formulario
		$frm->add($this->header);
		$frm->closediv(); //list_head
		
		
		// Detalle de la query.
		$frm->opendiv('list_body');
		if (method_exists($this->tbdef_V3,'query')) $dataset = new bas_sql_myqrydataset($this->tbdef_V3);
		else $dataset = new bas_dat_arraydataset(); 
		$table = new bas_htm_table($this->tbdef_V3, $dataset);
		
		$frm->add($table);
		$frm->closediv(); //list_body
		
		// Subformularios
		foreach($this->subforms_V3 as $subform) $subform->OnPaint($frm);
		
		// Pie
		$frm->opendiv('list_foot');
		$frm->add($this->footer_V3);
		$frm->closediv();
		$frm->printme();
	}
	
	public function OnAction_V3($action, $data){

		if ($ret = $this->menu->OnAction($action,$data)) return $ret;
		
		
		if (strpos($action,'#') !== false){
			list($id, $action) = explode('#',$action);
			$this->subforms[$id]->OnAction($action, $data);
			
		} else {
			
			if (isset($data['selected']) && method_exists($this->tbdef, 'setkeyselected')){
				$this->tbdef->setkeyselected($data);			
			}
			
			switch($action){
				case 'primero': 	$this->tbdef->go('first'); break;
				case 'anterior':	$this->tbdef->go('previouspage'); break;
				case 'siguiente':	$this->tbdef->go('nextpage'); break;
				case 'ultimo':		$this->tbdef->go('last'); break;
				
				case 'mostrar':
					return array('open', 'bas_dlg_select', 'initdlgselect', array('cols'=>$this->tbdef->getcols('*')));
				case 'selectset':
					$selected = $order = array();
					foreach($data as $col){
						$order[] = $col['id'];
						if ($col['selected']) $selected[] = $col['id'];
					}
					
					$this->tbdef->setcolorder($order);
					$this->tbdef->select(false, '*');
					$this->tbdef->select(true, $selected);
					break;
					
				case 'filtros':
					return array('open', 'bas_dlg_filter', 'initdlgfilter', $this->tbdef->getcols('*'));
				case 'filterset':
					foreach($data as $key => $filter){
						if (is_array($filter)) $this->tbdef->setfilter($filter['filter'], $filter['id']);
						else $this->tbdef->setfilter($filter, $key);
					}
					break;
					
				case 'orden': case 'orderset':
					break;
	
					
				// Para el autokeyfilter en las listas.	
				case 'lookup':
					if (method_exists($this->tbdef,'getautokeyfilter')) $filter = $this->tbdef->getautokeyfilter(); else $filter = array(); 
					return (array('open',"frm_${data['lookup']}lookup", "seek", $filter));
					
				case 'seek': case 'setvalues':
					if (method_exists($this->tbdef,'setkeyfromrecord')) $this->tbdef->setkeyfromrecord($data);
					break;
				
			}
		}
	}
	
}
?>
