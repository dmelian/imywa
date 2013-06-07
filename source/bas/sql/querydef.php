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
/**
 * Esta es una clase de prueba para soportar conjuntos de datos formados por querys de varias tablas
 * en contraposición con bas_dat_tabledef que se definió para una sola tabla.
 * En principio como no estamos seguros de como saldrá un intento de fusión de las dos, y como la primera
 * funciona muy bien lo hemos pasado a una clase nueva
 * Esto salió por primera vez en vigilancia. Las visitas activas.
 */
class bas_sql_querydef extends bas_dat_tabledef {
	public $tables = array();
	protected $pivot;
	protected $sorting = array();
	protected $position = array();
	protected $conditions = array(); // condiciones directas a where sin pasar por filtros.
	protected $extrajoinconditions = array(); //Condiciones adicionales de los joins.
	protected $lasttable;
	protected $key;
	protected $order;
	protected $extraselect = '';

	public function __construct($caption, $id=''){
		global $CONFIG;
		parent::__construct($caption, $id);
		$this->position['recsxpag'] = 100000;//$CONFIG['RECSXPAG'];
		$this->position['currec'] = 0; // Si acaso algún locate sobre la tabla.
		$this->position['numrows'] = $this->position['recsxpag'];
	}

// TABLES
	
	public function add($table){
		$this->tables[] = array('table'=>$table);
		$this->lasttable = $table;
	}
	
	/**
	 * Añade una tabla relacionada con las anteriores
	 *
	 * @param string $table - tabla a añadir
	 * @param array $relatedfields - array con los campos de la relacion
	 * @param string $relatedtable - tabla con la que se relaciona
	 */
	public function addrelated($table, $relatedfields='', $relatedtable='', $jointype='left'){
		if (!$relatedtable) $relatedtable = $this->lasttable;
		if (!$relatedfields){
			$relatedfields = array(array('field'=>$table, 'relatedfield'=>$table));
		} elseif (!is_array($relatedfields)) {
			$fields = array();
			foreach(explode(',', $relatedfields) as $field){
				$fields[]=array('field'=>trim($field), 'relatedfield'=>trim($field));
			}
			$relatedfields = $fields;
		}
		$this->tables[] = array('table'=>$table, 'relatedtable'=>$relatedtable, 'relatedfields'=>$relatedfields, 'jointype'=>$jointype);
		$this->lasttable = $table;
	}
	
	public function addmanual($table, $condition, $jointype='left'){
		$this->tables[] = array('table'=>$table, 'manual'=>true, 'condition'=>$condition, 'jointype'=>$jointype);
		$this->lasttable = $table;
	}
	
	public function addextrajoincondition($extracondition, $id=''){
		if (!$id){$id=$this->lasttable;}
		$this->extrajoinconditions[$id] = $extracondition;
	}
	
// FIELDS
	
	public function addcol($caption, $id='', $table=''){
			parent::addcol($caption, $id);
			if (!$table) $table=$this->lasttable;
			$this->lasttable = $table;
			parent::setproperty('table', $table);
//			$this->selected[] = array('id' => $this->lastid, 'selected' => $this->defselected);
//			$this->filters[$this->lastid] = array('id' => $this->lastid, 'filter' => '');
	}

	public function addextraselect($expresion){
		// Añade expresiones al select que no son tratados como columnas pero que se necesitan como campos internos.
		// Se ha creado para poder tratar de una forma sencilla las plantillas dinámicas.
		$this->extraselect = $this->extraselect ? "{$this->extraselect}, $expresion" : $expresion;
	}
	
	public function delextraselect(){$this->extraselect = '';}
	
// PRIMARY KEYS

	public function setkey($key){
		if (isset($this->key)) unset($this->key);
		$this->key=array();
		if(is_array($key)) foreach($key as $field) $this->key[]=array('id'=>$field);
		else foreach (explode(',', $key) as $field) $this->key[]=array('id'=> trim($field));
	}
	
	public function setkeyvalue($value){
		$i=0;
		if(is_array($value)) foreach($value as $fieldvalue) $this->key[$i++]['value']=$fieldvalue;
		else foreach (explode(',', $value) as $fieldvalue) $this->key[$i++]['value']=trim($fieldvalue);
	}
	
	public function setkeyfromrecord($record){
		for ($i=0; $i<count($this->key); $i++) {
			if(isset($record[$this->key[$i]['id']])) $this->key[$i]['value'] = $record[$this->key[$i]['id']];
		}
	}
	
	public function getrecordkey($record = null){
		$rkey = array();
		for ($i=0; $i<count($this->key); $i++) {
			if (is_null($record)){
				if (isset($this->key[$i]['value'])) $rkey[$this->key[$i]['id']] = $this->key[$i]['value'];
			} else {
				if(isset($record[$this->key[$i]['id']])) $rkey[$this->key[$i]['id']] = $record[$this->key[$i]['id']];
			}
		}
		return $rkey;
	}
	
	public function getrecordfromkey(&$exists){
		if ($where = $this->whereforkeyclause()){
			$qry = new bas_sql_myquery($this->selectclause(true) . $this->fromclause() . $where);
			$exists = $qry->success;
			if ($exists) return $qry->result;
		}
		$exists = false;	
		$rec = $this->getemptyrec();
		foreach($this->key as $key) if (isset($key['value'])) $rec[$key['id']] = $key['value'];
		return $rec;	
	}

	private function whereforkeyclause(){
		$where = ''; $sep = ''; $complete = true;
		foreach($this->key as $key){
			if (isset($key['value'])){
				$where .= "$sep{$this->cols[$key['id']]['table']}.{$key['id']} = '{$key['value']}'"; 
				$sep = ' and ';
			} else {
				$complete = false; break;
			}
		}
		$where = $where ? " where $where" : " where 1";
		return $complete ? $where : false;
	}
	
	
// AUTO KEY FILTER	
	
	// El key auto filter cuando está habilitado crea la condición para que sólo aparezcan los registros con ese valor que tenga la clave.
	public function setautokeyfilter($keyfiltered, $filter = true){
		if (isset($this->key)){
			if (! is_array($keyfiltered)) $keyfiltered = explode(',', $keyfiltered);
			for ($i=0; $i<count($keyfiltered); $i++) $keyfiltered[$i] = trim($keyfiltered[$i]);
			for ($i=0; $i<count($this->key); $i++) if (in_array($this->key[$i]['id'], $keyfiltered)) {
				$this->key[$i]['filtered']=$filter;
				$this->setproperty('keyfiltered', $filter, $this->key[$i]['id']);
			}
		}
	}
	
	public function getautokeyfilter(){
		$filter = array();
		if (isset($this->key)){
			for ($i=0; $i<count($this->key); $i++) {
				if ((isset($this->key[$i]['filtered']) && $this->key[$i]['filtered'])
					|| isset($this->key[$i]['select'])) {
					if (isset($this->key[$i]['value'])) $filter[$this->key[$i]['id']] = $this->key[$i]['value']; 
				}
			}
		}
		return $filter;		
	}
	
	public function getautokeycondition(){
		$autokeycondition = ''; $sep = '';
		if (isset($this->key)){
			for ($i=0; $i<count($this->key); $i++) {
				if (isset($this->key[$i]['filtered']) && $this->key[$i]['filtered']) {
					$autokeycondition .= $sep . $this->cols[$this->key[$i]['id']]['table'] .".". $this->cols[$this->key[$i]['id']]['id']
						. " = '" . $this->key[$i]['value'] . "'";
					$sep = ' and ';
				}
			}
		}
		return $autokeycondition;
	}
	
// AUTO KEY SELECT	
	public function setautokeyselect($keyselect){
		if (isset($this->key)){
			if (! is_array($keyselect)) $keyselect = explode(',', $keyselect);
			for ($i=0; $i<count($keyselect); $i++) $keyselect[$i] = trim($keyselect[$i]);
			$sel = array();
			$orden=0;
			for ($i=0; $i<count($this->key); $i++) if (in_array($this->key[$i]['id'], $keyselect)){
				$sel[] = $this->key[$i]['id'];
				$this->key[$i]['select'] = $orden++;
			}
			$exp = "concat_ws(','";
			for ($i=0; $i<count($sel); $i++){
				if (isset($this->cols[$sel[$i]])) $exp .= ", {$this->cols[$sel[$i]]['table']}.{$sel[$i]}";
			}
			$exp .= ')';
			$this->addcol('Sel','selected');
			$this->setproperty('system');
			$this->setproperty('expresion',$exp);
			$this->setproperty('colwidth', 20);
			$this->settemplate('radio',array('keyselect'=>$sel));
			$this->setcolorder('selected',1,false);			
		}
	}
	
	public function setkeyselected($data){
		
		$values = explode(',',$data['selected']);
		for($i=0; $i<count($this->key); $i++) {
			if (isset($this->key[$i]['select'])){
				if (isset($values[$this->key[$i]['select']])) $this->key[$i]['value']=$values[$this->key[$i]['select']];
				else unset($this->key[$i]['value']);
			}
		}
	}

	public function unsetkeyselected(){
		
		for($i=0; $i<count($this->key); $i++) {
			if (isset($this->key[$i]['select'])) unset($this->key[$i]['value']);
		}
	}
	
	
	
	public function iskeyselected($rec){
		$iskey = true;
		for($i=0; $i<count($this->key); $i++) {
			if (isset($this->key[$i]['select'])){
				$iskey = isset($this->key[$i]['value'])
					&& isset($rec[$this->key[$i]['id']])
					&& $this->key[$i]['value'] == $rec[$this->key[$i]['id']]; //TODO: La clave de debería ver siempre.
				if (!$iskey) break;
			}
		}
		return $iskey;		
	}
	

// COLUMN FILTERS
	
	public function setfilter($filter, $id=''){
		if (!$id) $id = $this->lastid;
		$this->cols[$id]['filter'] = $filter;
	}
	
	public function getfilter($id){
		return isset($this->cols[$id]['filter']) ? $this->cols[$id]['filter'] : '';
	}
	
	public function unsetfilter($ids=''){
		if (!$ids) $ids = array($this->lastid);
		elseif (!is_array($ids)) $ids = $ids == '*' ? array_keys($this->cols) : explode(',',$ids);
		foreach ($ids as $id) {
			if (isset($this->cols[$id]['filter'])) unset($this->cols[$id]['filter']);
		}
	}
	
/*	public function getfilterinfo(){ //OBSOLETO - utilizar getcols('*');
		$filters = array();
		foreach ($this->filters as $id => $filter) {
			$filter['caption'] = $this->cols[$id]['caption'];
			$filters[$id] =  $filter;
		}
		return $filters;
		
	}
	
	public function setfilterinfo($filterinfo){ //OBSOLETO - Utilizar set/unsetfilter
		$this->filters = $filterinfo;
	}
*/	

// CONDITIONS

//TODO: PONER LAS CONDICIONES CON NIVELES CON UNA CLASE CONDICIÓN CON UN ID, OPERADOR Y CONDICIONES TAL CUAL ESTA.
//TODO: AL AÑADIR CONDICIONES SE PUEDE IDENTIFICAR UN ID.ID.ID PARA BAJAR VARIOS NIVELES.	
	public function addcondition ($condition, $id=''){
		if ($id) {$this->conditions[$id]=$condition;}
		else {$this->conditions[]=$condition;}
	}
	
	
// NAVIGATE
	
	public function go($pos){
		switch($pos){
			case 'first':
				$this->position['currec']=0;
				break;
			case 'previouspage':
				$this->position['currec'] -= $this->position['recsxpag'];
				if ($this->position['currec']<0) $this->position['currec']=0;
				break;
			case 'previous':
				$this->position['currec']--;
				if ($this->position['currec']<0) $this->position['currec']=0;
				break;
			case 'next':
				$this->position['currec']++;
				if ($this->position['currec'] >= $this->position['numrows']) $this->position['currec']=$this->position['numrows']-1;
				if ($this->position['currec']<0) $this->position['currec']=0;
				break;
			case 'nextpage':
				$this->position['currec'] += $this->position['recsxpag'];
				if ($this->position['currec'] >= $this->position['numrows']) $this->position['currec']=$this->position['numrows']-1;
				if ($this->position['currec']<0) $this->position['currec']=0;
				break;
			case 'last':
				$this->position['currec'] = $this->position['numrows'] -1;
				if ($this->position['currec']<0) $this->position['currec']=0;
				break;
		}
		
	}
	
	public function setrowcount($rowcount){
		$this->position['numrows'] = $rowcount;
	}

// POSITION

	public function setposition($currec, $recsxpag=0){
		$this->position['currec'] = $currec;
		if ($recsxpag) $this->position['recsxpag']= $recsxpag;
	}

// ORDER
	public function setorder($order){
		$this->order = $order;
	}
	
	
// 	QUERY	
// ------------------------------------------------------------------
	/**
	 * Return the actual query of the table def.
	 *
	 */
	public function query($nolimits = false){
		$qry = $this->selectclause() . $this->fromclause() . $this->whereclause() . $this->orderclause();
		if (!$nolimits) $qry .= $this->limitclause();
		return $qry;
	}
	
// SELECT
	public function selectclause($all = false){
		$ret = $prefix = '';
		foreach ($this->cols as $col){
			if ($all || (((isset($col['selected']) && $col['selected']) || isset($col['system'])) && !isset($col['pivot']))){
				if (isset($col['aliasof'])) {
					$ret .= "$prefix{$col['table']}.{$col['aliasof']} as {$col['id']}" ;
				} else if (isset($col['expresion'])) {
					$ret .= "$prefix{$col['expresion']} as {$col['id']}" ;
				} else {
					$ret .= "$prefix{$col['table']}.{$col['id']}";
				}
				$prefix = ', ';
			}
		}
		if ($this->extraselect) $ret = "$ret, {$this->extraselect}";
		if ($ret) return "select SQL_CALC_FOUND_ROWS $ret";
		else return "select 0";
	}

// FROM
	
	public function fromclause(){
		
		$tables = $this->jointables();
		return $tables ? " from $tables" : '';
		
	}
	
	protected function jointables(){

		$first = true;
		$ret = '';
		foreach($this->tables as $table) {
			if (isset($table['relatedtable']) && !$first){
				$prefix='';
				$condition = '';
				foreach($table['relatedfields'] as $field){
					$condition .= $prefix.$table['relatedtable'].'.'.$field['relatedfield']
						.' = '.$table['table'].'.'.$field['field'];
					$prefix = ' and ';
				} 
				if ($condition) {  
					$ret .= " ${table['jointype']} join ${table['table']} on $condition";
					if (isset($this->extrajoinconditions[$table['table']])) {
						$ret .= " and {$this->extrajoinconditions[$table['table']]}";
					}
				} else { $ret .= ", ${table['table']}"; }
				
			} elseif(isset($table['manual'])) {
				$ret .= " ${table['jointype']} join ${table['table']} on ${table['condition']}";
				
			} else { $ret .= ($first ? '' : ', ').$table['table'];	}
			$first=false;
		}
		return $ret;
		
	}
	
	

// WHERE
	
	public function whereclause(){
		
		$whereconditions = ''; $prefix = '';
		foreach ($this->conditions as $condition){
			$whereconditions .= "$prefix($condition)";
			$prefix = ' and ';
		}
		$psql = new bas_sql_filtertowhere($this->cols);
		$wherefromfilters = $psql->filtertowhere();
		$wherefromautokey = $this->getautokeycondition();
		// else $wherefromfilters = ''; - en el caso que no hubiera filtros 
		
		$where='';
		if (!empty($whereconditions)) { $where = " where ($whereconditions)"; }
		if (!empty($wherefromautokey)) {
			if ($where) { $where .= " and ($wherefromautokey)"; }
			else { $where = " where ($wherefromautokey)"; }
		}
		if (!empty($wherefromfilters)) {
			if ($where) { $where .= " and ($wherefromfilters)"; }
			else { $where = " where ($wherefromfilters)"; }
		}
		return $where;
	}
	
// GROUP BY	

// LIMIT
	protected function limitclause(){
		if (isset($this->position['currec'])) $ret = $this->position['currec'];
		if (isset($this->position['recsxpag'])) $ret .= ',' . $this->position['recsxpag'];
		if ($ret) $ret = ' limit '.$ret;
		return $ret;
	}

// ORDER
	public function orderclause(){
		if (isset($this->order) && !empty($this->order)){
			return " order by $this->order";		
		} else return '';	
	}
	
// MISCELANEUS
	
	public function refresh(){
		// realiza los refrescos necesario. 
		if (isset($this->pivot) && $this->pivot['auto']) {
			$this->refreshpivotcols($this->pivot['auto']);
		}
	}
	
}

?>
