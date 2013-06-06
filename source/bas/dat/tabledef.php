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
class bas_dat_tabledef{
	public $caption;
	public $id;
	
	public $hashead = true;
	public $hasfoot = false;
	/**
	 * Definición de la columna
	 * Usadas en bas_htm_table
	 * id:string - identificador del campo
	 * caption:string - Etiqueta descriptiva
	 * template:object - Plantilla que se aplicará al contenido object->stamp($data, $rec);
	 * headtemplate:string - Plantilla que se aplicará al caption para formar el encabezado.
	 * foot:variant - Dato que se pondrá como pie 
	 * foottemplate:string - Plantilla para el pié.
	 * 
	 * @var array asociativa de propiedades
	 */
	protected $rowproperties=array();
	public $cols=array();
	protected $tabs=array();
	protected $templatedefs=array();
	protected $spans=array(); //Son spans de colunmas o filas. Un array con identificadores de fila y columna y el número de filas y columnas que se unen.
	protected $rowid; //identificador de la columna que se utiliza como identificador de fila o registro.
	protected $lastid;
	protected $defaultselected=true;
	private $curtab;
	
	public function __construct($caption = '', $id=''){
		$this->caption = $caption;
		if (!$id) {
			$aux = new bas_aux_functions();
			$this->id = $aux->idfromcaption($caption);
		} else $this->id = $id;
	}

	public function export(){
		$exp= array();
		foreach(array('caption') as $prop) $exp[$prop]= $this->$prop;
		$exp['cols']= array();
		foreach($this->cols as $col) {
			$expcol= array();
			$expcol['id']= $col['id'];
			$expcol['caption']= $col['caption']; 
			$expcol['width'] = isset($col['width']) ? $col['width'] : 10;
			$exp['cols'][]= $expcol;
		}
		return $exp; 
	}	
	
// TABS
	
	public function gettabs(){ return $this->tabs; }
	
	public function opentab($tab){
		$this->tabs[]=$tab;
		$this->curtab = $tab;
	}

// COLUMNS

	public function getcols($tab=''){
		$cols = array();
		foreach($this->cols as $col) {
			if (($tab=='*' && !isset($col['system']))
				|| ($tab!='*' && ($tab == '' || $col['tab'] == $tab) 
					&& (((isset($col['selected']) && $col['selected']) || isset($col['system'])) && !isset($col['hidden']))
					) 
				) {
				$cols[$col['id']] = $col;
			}
		}
		return $cols;
	}

	public function getcolsbyproperty($property, $value){
		$cols = array();
		foreach($this->cols as $col) {
			if (isset($col[$property]) && ($col[$property] === $value || ($col[$property] === true && $col['id'] == $value) )) {
				$cols[$col['id']] = $col;
			}
		}
		return $cols;
	}
	
	
	public function addcol($caption, $id=''){
		if (!$id) {
			$aux = new bas_aux_functions();
			$id = $aux->idfromcaption($caption);
		}
		$this->cols[$id] = array('id'=>$id);
		$this->cols[$id]['caption'] = $caption;
		$this->lastid = $id;
		if ($this->curtab) $this->cols[$id]['tab'] = $this->curtab;
		$this->cols[$id]['selected'] = $this->defaultselected; 
	}
	
	public function delcol($id){
		unset ($this->cols[$id]);
	}
	
	public function delcolbyproperty($property, $value=null){
		foreach($this->cols as $key=>$col) {
			if (isset($col[$property]) && (is_null($value) || $col[$property] === $value)) unset($this->cols[$key]);
		}
	}

	public function getcol($id){ return $this->cols[$id];}
	
// COLUMNS ORDER
	
	public function setcolorder($ids,$order=0,$relative=true){
		
		// ids - lista de columnas a ordenar.
		if (!is_array($ids)) $ids=explode(',',$ids);
		$current = 1; $first = 0;
		$restoids = array();
		$keys = array_keys($this->cols);
		foreach($keys as $id){
			if (in_array($id, $ids)) {if(!$first) $first=$current; }
			else $restoids[]=$id;
			$current++;
		}
		if ($relative) $order = $order + $first;
		$newcols = array();
		$resto = reset($restoids);
		$ordenado = reset($ids);
		for ($current = 1; $current <= count($this->cols); $current++){
			if ($current < $order && $resto) {
				$newcols[$resto] = $this->cols[$resto];
				$resto = next($restoids);
			} elseif ($ordenado) {
				$newcols[$ordenado] = $this->cols[$ordenado];
				$ordenado = next($ids);
			} elseif ($resto) {
				$newcols[$resto] = $this->cols[$resto];
				$resto = next($restoids);
			} else {
				$LOG->log("BAS_DAT_TABLEDEF. Error. Se llegó al final de lista antes de tiempo.\n$log");
			}
		}
		$this->cols = $newcols;
		
	}

// COLUMNS SELECTION	
	
	public function select($selected=true, $ids=''){
		if (!$ids) $ids = array($this->lastid);
		elseif (!is_array($ids)) $ids = $ids == '*' ? array_keys($this->cols) : explode(',',$ids);
		foreach ($ids as $id) {
			if (isset($this->cols[$id])) $this->cols[$id]['selected'] = $selected;
		}
	}
	
	public function defaultselect($selected=true){
		$this->defaultselected = $selected;
	}

// PROPERTIES	
	
	public function setproperty($properties, $value=true, $ids=''){
		if (!is_array($properties)) $properties = array($properties => $value);
		if (!$ids) $ids = array($this->lastid);
		elseif (!is_array($ids)) $ids = $ids == '*' ? array_keys($this->cols) : array($ids);
		foreach($ids as $id){
			foreach($properties as $property => $value) {
				$this->cols[$id][$property]=$value;
				if ($property == 'rowid') {$this->rowid = $id;} 
			}
		}
	}
	
	public function getproperty($id, $property, $default=''){
		if ($id == '*'){
			$return = array();
			foreach($this->getcols() as $col){
				$return[$col['id']] = isset($col[$property]) ? $col[$property] : $default;
			}
			return $return;
						
		} else {
			if (isset($this->cols[$id][$property])) return $this->cols[$id][$property];
			else return null; 
		}
	}
	
	
// ROW PROPERTIES
	public function setrowproperty($property, $value){
		$this->rowproperties[$property] = $value;
	}	
	
	public function getrowproperty ($property){
		return isset($this->rowproperties[$property]) ? $this->rowproperties[$property] : false;
	}
	
// TEMPLATES	
/*	
	public function addtemplatedef($id, $template, $properties=array()){
		$this->templatedefs[$id] = array('template'=>$template, 'properties'=>$properties);
	}
*/	
	public function settemplate($template, $properties=array(), $ids=''){
		# para que funcione correctamente deben estar ya cargadas todas las propiedades de la columna.
		if (!$ids) $ids = array($this->lastid);
		elseif (!is_array($ids)) $ids = $ids == '*' ? array_keys($this->cols) : array($ids);
		foreach($ids as $id){
			if (strtoupper($template) == 'NONE') {
				unset($this->cols[$id]['template']);
				unset($this->cols[$id]['template_properties']);
			} else {
				$this->cols[$id]['template'] = $template;
				$this->cols[$id]['template_properties'] = $properties;
			}
		}
	}
	
// COL - ROW SPAN	
	
	public function setrowid ($id){ $this->rowid = $id;}
	public function getrowid (){ return isset($this->rowid) ? $this->rowid : false;}
	
	public function setspan ($row, $col, $span, $type='col'){
		if (is_int($row)) $row = "L$row";
		$spanid = "$row:$col";
		$this->spans[$spanid] = array('type'=>$type, 'length'=>$span);
	}
	
	public function getspan ($row, $col){
		if (is_int($row)) $row = "L$row";
		$spanid = "$row:$col";
		if (isset($this->spans[$spanid])) return $this->spans[$spanid];	else return false;
	}

// PIVOT
/*	Id columna que contiene el campo que se quiere poner en el mismo registro
	Value columna que tiene el campo que se quiere mostrar
	Las columnas marcadas con la propiedad 'pivot' son las que se crean dinamicamente con el pivotdataset.
		El valor de esta columna es el valor que debe tener el campo value para seleccionar esa columna. default, para cualquier valor.
*/	
	public function setpivot($id, $value, $func='replace', $auto = false){
		$this->pivot = array('id'=>$id, 'value'=>$value, 'function'=>$func, 'auto'=>$auto);
		$this->setproperty('system','',array($id,$value));
		$this->setproperty('hidden','',array($id,$value)); // para que no se muestren
	}
	
	public function getpivotinfo(){
		if (isset($this->pivot)) {
			$pivot = $this->pivot;
			$pivot['cols'] = array();
			$default = false;
			foreach($this->cols as $col){
				if (isset($col['pivot'])) {
					if ($col['pivot'] == 'default'){
						$default = true;
						$pivot['default'] = $col['id'];		
					} else {
						$pivot['cols'][] = array('id'=>$col['id'], 'matchvalue'=>$col['pivot']);
					} 	
				}
			}
			if (!$default) $pivot['default'] = '';
			return $pivot;			
		} else return false;
	}
	
	public function refreshpivotcols($colnamepatern=''){
		//Automatic refresh al pivot cols.
		$ds = new bas_sql_myqrydataset($this);
		if ($pivotvalues = $ds->getpivotvalues()){
			$this->delcolbyproperty('pivot');
			foreach ($pivotvalues as $value){
				if($colnamepatern) eval("\$colname = \"$colnamepatern\";");
				else $colname = $value;
				$this->addcol($colname);
				$this->setproperty('pivot', $value);
			}
		}
		$ds->close();
	}
	
// MISCELANEOUS	
	
/*	public function getemptyrec(){
		$rec = array();
		foreach ($this->cols as $col) $rec[$col['id']] = null;
		return $rec;
	}
*/	
	public function getemptyrec(){
		$rec = array();
		foreach ($this->cols as $col) {
			if (!isset($col['pivot'])){
				$rec[$col['id']] = null;
			}
		}
		return $rec;
	}
	
	public function getemptypivotrec(){
		$rec = array();
		foreach ($this->cols as $col) {
			if ($col['id'] != $this->pivot['id'] && $col['id'] != $this->pivot['value']){
				$rec[$col['id']] = null;
			}
		}
		return $rec;
	}
	
	
	

}
?>
