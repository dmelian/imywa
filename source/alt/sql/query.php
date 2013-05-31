<?php 
class alt_sql_query {
	
	public $fields= array();
	public $tables= array();
	
	public $defaultDb;
	public $currTable;
	public $currField;

	public function __construct($db= ''){
		global $_SESSION;
		$_SESSION->apps[$_SESSION->currentApp]->getMainDb($host, $database);
		$this->defaultDb= $db ? $db : $database;
	}

// TABLES	
	
	public function addTable($table, $db=''){ 
		$this->addTableAs($table, $table, $db); 
	}
	
	public function addTableAs($table, $alias, $db=''){
		if (!$db) $db= $this->defaultDb;
		$this->tables[$alias]= array('id'=>$alias, 'name'=>$table, 'db'=>$db);
		$this->currTable= $alias;
	}
	
	public function addRelatedTable($table, $relatedFields='', $relatedTable='', $joinType='left', $db=''){
		$this->addRelatedTableAs($table, $table, $relatedFields='', $relatedTable='', $joinType='left', $db='');
	}
	
	public function addRelatedTableAs($table, $alias, $relatedFields='', $relatedTable='', $joinType='left', $db=''){
		if (!$relatedTable) $relatedTable= $this->currTable;
		if (!$relatedFields) $relatedFields= array(array('field'=>$table, 'relatedField'=>$table));
		elseif (!is_array($relatedFields)) {
			$fields= array();
			foreach(explode(',', $relatedFields) as $field){
				$fields[]= array('field'=>trim($field), 'relatedField'=>trim($field));
			}
			$relatedFields= $fields;
		}
		$this->addTableAs($table, $alias, $db);
		$this->tables[$alias]+= array('relatedTable'=>$relatedTable, 'relatedFields'=>$relatedFields, 'joinType'=>$joinType);
	}
	
	public function setJoinExpression($expression, $replace=true, $id=''){
		if(!$id) $id= $this->currTable;
		$this->tables[$id]+= array('joinExpression'=>$expression, 'replaceJoinExpression'=>$replace);
	}

	public function getFromExpression(){
		$first= true;
		$fromExpression= $joinExpression= '';
		foreach($this->tables as $table) {
			$separator=", ";
			if (isset($table['relatedTable'])){
				$expressionSeparator= '';
				foreach($table['relatedFields'] as $field){
					$joinExpression.= $expressionSeparator
						. "{$table['relatedTable']}.{$field['relatedField']} = {$table['id']}.{$field['field']}"
					;
					$expressionSeparator= ' and ';
				}
				if (isset($table['joinExpression'])) {
					$joinExpression= isset($table['replaceJoinExpression']) ?
						$table['joinExpression'] : "($joinExpression) and ({$table['joinExpression']})"; 
				}
				$separator= " {$table['joinType']} join ";
			}
			if ($first) {$separator= $joinExpression= ''; $first= false;}
			$fromExpression.= "$separator{$table['db']}.{$table['name']}";
			if ($table['name'] != $table['id']) $fromExpression.= " as {$table['id']}";
			if ($joinExpression) {$fromExpression.= " on $joinExpression"; $joinExpresion= '';}  
		}
		return $fromExpression;
	}
	
	
	
	
// FIELDS

	public function addField($name, $type, $table=''){
		$this->addFieldAs($name, $name, $type, $table);
	}
	
	public function addFieldAs($name, $alias, $type, $table=''){		
		if (!$table) $table= $this->currTable;
		$this->addExpression("$table.$name", $alias, $type);
		$this->fields[$alias]['table']= $table;
	}
	
	public function addExpression($expression, $alias, $type){
		$this->fields[$alias]= array('id'=>$alias, 'expression'=>$expression, 'type'=>$type);
		$this->currField= $alias;
	}	
	
	public function getSelectExpression(){
		$selectExpression= $separator= '';
		foreach ($this->fields as $field){
			$selectExpression.= "$separator{$field['expression']}";
			if (!isset($field['table']) || ($field['expression'] != "{$field['table']}.{$field['id']}")) {
				$selectExpression.= " as {$field['id']}";
			}
			$separator= ', ';
		}
		return $selectExpression;
	}

	
// FILTERS	

	public function setFieldFilter($name, $type, $table=''){
		$this->addFieldAs($name, $name, $type, $table);
		if (!$table) $table= $this->currTable;
		$this->addExpression("$table.$name", $alias, $type);
		$this->fields[$alias]['table']= $table;
	}
	
	public function addFilter($expression, $alias, $type){
		$this->fields[$alias]= array('id'=>$alias, 'expression'=>$expression, 'type'=>$type);
		$this->currField= $alias;
	}	
	
	public function getWhereExpression(){
		return '';
	}

// QUERY
	
	public function getQuery($options=array()){
		$fromExpression= $this->getFromExpression();
		
		$selectExpression= $this->getSelectExpression();
		if (!$selectExpression) $selectExpression= $fromExpression ? '*' : '0';
		
		$query= "select";
		if (isset($options['countRows'])) $query.= ' SQL_CALC_FOUND_ROWS';
		$query.= " $selectExpression";
		if (isset($options['extraSelectExpression'])) $query.= ", {$options['extraSelectExpression']}";
		
		if ($fromExpression) $query.= " from $fromExpression";
		
		if ($whereExpression= $this->getWhereExpression()) $query.= " where $whereExpression";
		
		return $query; 
	}
	
	
}

class alt_sql_mixquery {
/////////////////////////////////////////////////////// BASIC QUERY

	protected $position = array();
	protected $extrajoinconditions = array(); //Condiciones adicionales de los joins.
	protected $lasttable;
	public $key;
	protected $extraselect = '';
	protected $id;
	protected $caption;
	protected $maindb;

///////////////////////////////////////////////////////// QUERY DEF
	
	protected $pivot;
	protected $sorting = array();
	protected $conditions = array(); // condiciones directas a where sin pasar por filtros.
	protected $group = array();
	public $order;
// 	public $db;

/////////////////////////////////////////////////////// BASIC QUERY


// TABLES
	
	
	public function getdb($tablename){
		foreach($this->tables as $table) if ($table['table'] == $tablename) return $table['db'];
		return ''; 
	}
	
	/**
	 * Añade una tabla relacionada con las anteriores
	 *
	 * @param string $table - tabla a añadir
	 * @param array $relatedfields - array con los campos de la relacion
	 * @param string $relatedtable - tabla con la que se relaciona
	 */
	public function setAttColum($id,$att,$value){
		$this->cols[$id]->setAttr($att,$value);	
	}
	
// FIELDS
	
	
	public function getcols(){
		return $this->cols;
	}

	public function addextraselect($expresion){
		// Añade expresiones al select que no son tratados como columnas pero que se necesitan como campos internos.
		// Se ha creado para poder tratar de una forma sencilla las plantillas dinámicas.
		$this->extraselect = $this->extraselect ? "{$this->extraselect}, $expresion" : $expresion;
	}
	
	public function delextraselect(){$this->extraselect = '';}

// PRIMARY KEYS

	public function setkey($key){ //### Podría cambiarse el pk[][id] = valor_campo por pk[i] = valor_campo
		if (isset($this->key)) unset($this->key);
		$this->key=array();
		if(is_array($key)) foreach($key as $field) $this->key[]=array('id'=>$field);
		else foreach (explode(',', $key) as $field) $this->key[]=array('id'=> trim($field));
	}
	
	public function setkeyvalue($value){  //### Correccion: Camiar pk a pk[campo] = valor;
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
	
	public function setkeyValueB($record = null){ // ### A debatir.
		if (isset($record)){
			for ($i=0; $i<count($this->key); $i++) {
				if(isset($record[$this->key[$i]['id']])){
					$this->key[$i]['value']= $record[$this->key[$i]['id']];
					$this->key[$i]['filtered']= true;
				}
			}
		}
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

	private function whereforkeyclause(){// ###!! Antencion: acceso a cols. Done
		$where = ''; $sep = ''; $complete = true;
		foreach($this->key as $key){
			if (isset($key['value'])){
				$where .= "$sep{$this->cols[$key['id']]->table}.{$key['id']} = '{$key['value']}'"; 
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
				$this->setproperty('keyfiltered', $filter, $this->key[$i]['id']); // ### Atencion!!
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
					$autokeycondition .= $sep . $this->cols[$this->key[$i]['id']]->table .".". $this->cols[$this->key[$i]['id']]->id // ### Atencion. Accede a cols. Done
						. " = '" . $this->key[$i]['value'] . "'";
					$sep = ' and ';
				}
			}
		}
		return $autokeycondition;
	}
	
	
// Auto key from current record.
	public function getautokeyRecord($record){ // $record = asociative vector
		$autoKey=array();
		if (isset($this->key) && isset($record)){
			for ($i=0; $i<count($this->key); $i++){ 
				if (isset($record[$this->key[$i]['id']])){	
					$autoKey[$this->key[$i]['id']] = $record[$this->key[$i]['id']]; 
				}
			}
		}
		return $autoKey;
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
				if (isset($this->cols[$sel[$i]])) $exp .= ", {$this->cols[$sel[$i]]->table}.{$sel[$i]}";  // ### Atencion. Accede a cols
			}
			$exp .= ')';
		/*	$this->addcol('Sel','selected');// ### Eliminar este fragmento
			$this->setproperty('system');
			$this->setproperty('expresion',$exp);
			$this->setproperty('colwidth', 20);
			$this->settemplate('radio',array('keyselect'=>$sel));
			$this->setcolorder('selected',1,false);			*/
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

	public function getemptyrec(){
		$rec = array();
		foreach ($this->cols as $id => $col) {
//			if (!isset($col['pivot'])){
				$rec[$id] = null;
//			}
		}
		return $rec;
	}
	
	
	
///////////////////////////////////////////////////////// QUERY DEF
	

	
	public function getMaindb(){
		return $this->maindb;
	}
	
	
	public function existField($id){
		if (isset($this->cols[$id])) return true;
		else false;
	}	
	
	public function  getField($id){
		if (isset($this->cols[$id])) return $this->cols[$id];
		else NULL;
	}
	
	public function getAllfield(){
		return $this->cols;
	}
	
	protected function getTable($item){
        foreach ($this->tables as $table){
            if ($table["table"] == $item) return $table;
        }
        return NULL;
	
	}
	
	/*
	Campos a tener en field_type:
		filter
		aliasof
		expresion  ###
		selected
		tipo
		name
		caption
		editable
		value
		visible
		enum	###?
		table
		pivot   ###
		id		
		system  ###
		fvalue
		ivalue		
	*/
// COLUMN FILTERS
	
	public function setfilter($filter, $id=''){
		if (!$id) $id = $this->lastid;
		if (isset($this->cols[$id])){ $this->cols[$id]->filter = $filter;  // ### mediante funcion¿?
			global $_LOG;
			$_LOG->log("Querydef::setfiler. filtro realizado.". $this->cols[$id]->filter." en el campo $id");
		}
	}
	
	public function setfilterRecord($filter){
		foreach($filter as $id => $value){
			if (isset($this->cols[$id])){
				$this->cols[$id]->filter = $value;  // ### mediante funcion¿?
				global $_LOG;
				$_LOG->log("Querydef::setfiler. filtro realizado.". $this->cols[$id]->filter." en el campo $id");
			}
		}
	}
	
	public function getfilters(){
		$filters=array();
		foreach($this->cols as $id => $col) if ($col->filter != '') $filters[$id]= $col->filter;
		return $filters;
	}
	
	public function getfilter($id){
		global $_LOG;
			$_LOG->log("Querydef::getfiler. filtro ".$this->cols[$id]->filter." en el campo $id");
		return isset($this->cols[$id]->filter) ? $this->cols[$id]->filter : '';// ### Valor por defecto NULL.
	}
	
	public function unsetfilter($ids=''){
		if (!$ids) $ids = array($this->lastid);
		elseif (!is_array($ids)) $ids = $ids == '*' ? array_keys($this->cols) : explode(',',$ids);
		foreach ($ids as $id) {
			if (isset($this->cols[$id]->filter)) $this->cols[$id]->filter=NULL;  // ### Valor por defecto NULL.
		}
	}
	
// CONDITIONS

//TODO: PONER LAS CONDICIONES CON NIVELES CON UNA CLASE CONDICIÓN CON UN ID, OPERADOR Y CONDICIONES TAL CUAL ESTA.
//TODO: AL AÑADIR CONDICIONES SE PUEDE IDENTIFICAR UN ID.ID.ID PARA BAJAR VARIOS NIVELES.	
	public function addcondition ($condition, $id=''){
		if ($id) {$this->conditions[$id]=$condition;}
		else {$this->conditions[]=$condition;}
	}
	
	
// NAVIGATE     ### Obsoleto¿?
	
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
	
// Group by
    public function setGroup($field){
        $this->group[]=array("field"=>$field);
    }


	/*
select linfactura.comercializadora,linfactura.poliza,linfactura.nofactura, cronoHeaders.name, sum(linfactura.valor)
from contaluz.linfactura left join contaluz.factura on linfactura.nofactura = factura.nofactura inner 
join cronoHeaders on factura.fechafactura between cronoHeaders.fromDate  and cronoHeaders.untilDate 
group by linfactura.nofactura, cronoHeaders.name limit 15;

	*/
	
	
	
	

// 	QUERY	
// ------------------------------------------------------------------
	/**
	 * Return the actual query of the table def.
	 *
	 */
	public function query($order = true){
		global $CONFIG;
		//if(!$nolimits && $CONFIG['LSTTYPE'] == constant('LT_FULL_LIST')) $nolimits = false;
		$nolimits= false;
		$qry = $this->selectclause() . $this->fromclause() . $this->whereclause() . $this->groupclause();
		if ($order)$qry = $qry. $this->orderclause();
// 		if (!$nolimits) $qry .= $this->limitclause();
		return $qry;
	}
	
// SELECT
// 	public function groupclause($all = false){  // ### Pivot¿?¿?
//         $ret = $prefix = '';
//         foreach ($this->group as $col){
//             $table = $this->getTable($col["table"]);
//             $ret .= $prefix."{$table['db']}.{$table['table']}";
//         }
//         if ($ret) return " Group By $ret";//return "select SQL_CALC_FOUND_ROWS $ret"; //###
//         else return ""; //select 0
//     }
    
    
    
    public function groupclause(){  // ### Pivot¿?¿?
        $ret = $prefix = '';
        foreach ($this->group as $item){
            $col = $this->getField($item["field"]);
            $ret .= "$prefix{$col->db}.{$col->table}.{$col->id}";
            $prefix = ', ';
        }
        if ($ret) return " Group By $ret";//return "select SQL_CALC_FOUND_ROWS $ret"; //###
        else return ""; //select 0
    }
    

// FROM
	
	
	

// WHERE
	
	public function whereclause(){
		
		$whereconditions = ''; $prefix = '';
		foreach ($this->conditions as $condition){
			$whereconditions .= "$prefix($condition)";
			$prefix = ' and ';
		}
		$psql = new bas_sqlx_filtertowhere($this->cols);
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
		$order = "";
		$sep="";
		if (isset($this->order) && !empty($this->order)){
			foreach($this->order as $field => $dir){
				$order = $order. $sep.$field. " ". $dir;	
				$sep = ", ";
			}
		}
		$nelem = count($this->key);
		for ($ind=0;$ind<$nelem;$ind++){
			if (!isset($this->order[$this->key[$ind]['id']]))	$order = $order. $sep.$this->key[$ind]['id']. " ASC";
			$sep = ", ";
		}
		if ($order) $order= " order by $order";	
		return $order;
	
// 		if (isset($this->order) && !empty($this->order)){
// 			return " order by {$this->order}";
// 		} else return '';
	}
	
// MISCELANEUS        ### Posiblemente obsoleto
	
	public function refresh(){
		// realiza los refrescos necesario. 
		if (isset($this->pivot) && $this->pivot['auto']) {
			$this->refreshpivotcols($this->pivot['auto']);
		}
	}
	
}

?>