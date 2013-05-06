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
class bas_sqlx_querydef extends bas_sqlx_basicquery {
	protected $pivot;
	protected $sorting = array();
	protected $conditions = array(); // condiciones directas a where sin pasar por filtros.
	protected $group = array();
	public $order;
// 	public $db;

	public function __construct($caption="", $id=""){
		global $CONFIG;
		parent::__construct($caption, $id);
		$this->position['recsxpag'] = 10;//$CONFIG['RECSXPAG'];
		$this->position['currec'] = 0; // Si acaso algún locate sobre la tabla.
		$this->position['numrows'] = 10;//$this->position['recsxpag'];
// 		if ($db == "")$this->db = $this->maindb;
// 		else	$this->db = $db;
	}

	
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
	public function selectclause($all = false){  // ### Pivot¿?¿?
	//	global $_LOG;
		$ret = $prefix = '';
	//	$nl = count($this->cols);
		//$_LOG->log("Numero de columnas ".$nl);
		foreach ($this->cols as $col){
			if ($all || (((isset($col->selected) && $col->selected) || isset($col->system)) && !isset($col->pivot))){ // ### Done 
				if (isset($col->aliasof)) { // ### 
					$ret .= "$prefix{$col->db}.{$col->table}.{$col->aliasof} as {$col->id}" ;
				} else if ($col->expression) {
					$ret .= "$prefix{$col->expression} as {$col->id}" ;
				} else {
					$ret .= "$prefix{$col->db}.{$col->table}.{$col->id}";
				}
				$prefix = ', ';
			}
		}
		if ($this->extraselect) $ret = "$ret, {$this->extraselect}";
		if ($ret) return "select $ret";//return "select SQL_CALC_FOUND_ROWS $ret"; //###
		else return "select *"; //select 0
	}
	
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
                   if (isset($table['expresion'])){
                        $condition .= $prefix.$this->getdb($table['relatedtable']).'.'.$table['relatedtable'].'.'.$field['relatedfield']." ".$table['expresion'];
                   }
                   else{
                        $condition .= $prefix.$this->getdb($table['relatedtable']).'.'.$table['relatedtable'].'.'.$field['relatedfield']
                        .' = '.$table['db'].'.'.$table['table'].'.'.$field['field'];
                        
					}
					$prefix = ' and ';
				} 
				if ($condition) {  
					$ret .= " ${table['jointype']} join ${table['db']}.${table['table']} on $condition";
					if (isset($this->extrajoinconditions[$table['table']])) {
						$ret .= " and {$this->extrajoinconditions[$table['table']]}";
					}
				} else { $ret .= ", ${table['db']}.${table['table']}"; }
				
			} elseif(isset($table['manual'])) {
				$ret .= " ${table['jointype']} join ${table['db']}.${table['table']} on ${table['condition']}";
				
			} else { $ret .= ($first ? '' : ', ').$table['db'].'.'.$table['table'];	}
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
