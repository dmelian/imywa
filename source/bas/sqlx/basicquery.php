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
class bas_sqlx_basicquery {
	public $cols=array();
	public $tables = array();
	protected $position = array();
	protected $extrajoinconditions = array(); //Condiciones adicionales de los joins.
	protected $lasttable;
	public $key;
	protected $extraselect = '';
	protected $id;
	protected $caption;
	protected $maindb;

	public function __construct($caption="", $id=""){
 		global $_LOG;
		global $_SESSION;
		$this->id = $id;
		$this->caption = $caption;
		$_SESSION->apps[$_SESSION->currentApp]->getMainDb($host, $database);
		$_LOG->log("Las base de datos es $database");		
		$this->maindb = $database;
	}

// TABLES
	
	public function add($table,$db=''){
		global $_SESSION;
		if (!$db) $db=$this->maindb; else $bd= $_SESSION->apps[$_SESSION->currentApp]->getDbName($db);
		$this->tables[] = array('table'=>$table,'db'=>$db);
		$this->lasttable = $table;
	}
	
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
	public function addrelated($table, $relatedfields='', $relatedtable='',$db='', $jointype='left'){//###
		global $_SESSION;
		if (!$db) $db=$this->maindb; else $bd= $_SESSION->apps[$_SESSION->currentApp]->getDbName($db);
		if (!$relatedtable) $relatedtable = $this->lasttable;
		if (!$relatedfields){
			$relatedfields = array(array('field'=>$table, 'db'=>$db,'relatedfield'=>$table));
		} elseif (!is_array($relatedfields)) {
			$fields = array();
			foreach(explode(',', $relatedfields) as $field){
				$fields[]=array('field'=>trim($field), 'db'=>$db,'relatedfield'=>trim($field));
			}
			$relatedfields = $fields;
		}
		$this->tables[] = array('table'=>$table, 'relatedtable'=>$relatedtable,'db'=>$db, 'relatedfields'=>$relatedfields, 'jointype'=>$jointype);
		$this->lasttable = $table;
	}
	
	public function addmanual($table, $condition, $db='',$jointype='left'){
		global $_SESSION;
		if (!$db) $db=$this->maindb; else $bd= $_SESSION->apps[$_SESSION->currentApp]->getDbName($db);
		$this->tables[] = array('table'=>$table, 'manual'=>true, 'db'=>$db,'condition'=>$condition, 'jointype'=>$jointype);
		$this->lasttable = $table;
	}
	
	public function addextrajoincondition($extracondition, $id=''){
		if (!$id){$id=$this->lasttable;}
		$this->extrajoinconditions[$id] = $extracondition;
	}
	
	public function setAttColum($id,$att,$value){
		$this->cols[$id]->setAttr($att,$value);	
	}
	
// FIELDS
	
	public function addcol($id, $caption='', $table='',$pk='',$db='',$type='text',$aliasof=NULL){
		global $_LOG;
		// Codigo de tableded
		// ### Obtenemos el caption pre-establecido para el id indicado.
		if (!$id) {
			$aux = new bas_aux_functions();
			$id = $aux->idfromcaption($caption);
		}
		$this->lastid = $id;
		// Codigo de tableded
		
		if (!$table) $table=$this->lasttable;
		else	$this->lasttable = $table;
		
		//if (!$db) $db=$this->maindb;
		$db= $this->getdb($table); 
		
		//if (!$type) $type="text";
		switch ($type){
				case "text": case "enum": case "boolean": case "date": case "money": case "image": case "upload": case "number":case "date":
					$fieldtype = "bas_sqlx_field".$type;
					$field = new $fieldtype($id,$table,$db,$pk,$caption,$aliasof);//"bas_sqlx_fieldtype(X)"
					break;
				
				case null:
					$field = new bas_sqlx_fielddef($id,$table,$db,$pk,$caption,$aliasof);
					break;
					
				default:
					if (class_exists($type)) $field= new $type($id,$table,$db,$pk,$caption,$aliasof) ;
					else {
						$_LOG->log("Tipo de datos inexistente: $type. BasicQuery::addcol");
					}
					break;
		}
		$this->cols[$id] = $field;
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
	
	
}

?>
