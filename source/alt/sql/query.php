<?php 
class alt_sql_query {
	
	public $fields= array();
	public $tables= array();
	public $filters= array();
	public $filterMode= 'and';
	public $sorting= '';
	
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
	
	#relatedFields: array(field=> relatedField=>) or comma separated string if the name of the two fields are the same.
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
	
	#for a specific join expression of the last related add.
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
	
	#filterMode: and | or
	public function setFilterMode($filterMode){ $this->filterMode= trim($filterMode); }
	
	#$fieldFilters: comma separated field ids you want to filter to or array(field=> filter=>) 
	public function addFieldFilters($fieldFilters, $filterValues=array()){
		if (!is_array($fieldFilters)) {
			$filters= array();
			foreach(explode(',', $fieldFilters) as $filter) {
				$filterValue= isset($filterValues[$filter]) ? $filterValues[$filter] : '';
				$filters[]=array('field'=>trim($filter), 'filter'=>$filterValue);
			}
			$fieldFilters= $filters;
		}
		foreach($fieldFilters as $fieldFilter) $this->addFieldFilter($fieldFilter['field'], $fieldFilter['filter']);
	}
	
	public function addFieldFilter($field, $filter){
		if (isset($this->fields[$field])) $this->addFilterAs($this->fields[$field]['expression'], $field, $filter);
		//TODO ELSE LOG
	}
	
	#anomymous filter. Not deletable, not editable, not replaceable, fixed.
	public function addFilter($expression, $filter){ $this->addFilterAs($expression, '', $filter); }
	
	public function addFilterAs($expression, $id, $filter){
		if ($id) $this->filters[$id]= array('leftPart'=>$expression, 'filter'=>$filter, 'id'=>$id); #id only for reference to the user.
		else $this->filters[]= array('leftPart'=>expression, 'filter'=>$filter);
	}
	
	public function delFilter($id){ if (isset($this->filters[$id])) unset($this->filters[$id]); }
	
	public function getWhereExpression(){
		$whereExpression= ''; $logicOperator= '';
		$parser= new alt_sql_filterParser();
		foreach($this->filters as $filter){
			if ($filterExpression= $parser->parseFilter($filter['leftPart'], $filter['filter'])){
				$whereExpression.= "$logicOperator( $filterExpression )";
				$logicOperator= " {$this->filterMode} ";
			}
		}
		return $whereExpression;
	}

// SORTING

	#$sorting: fields identifiers comma separated string with an optional < or > for the direction. 
	public function sortBy($sorting){ $this->sorting= $sorting; }
	
	public function getOrderExpression(){
		$orderExpression= ''; $separator= '';
		foreach(explode(',', $this->sorting) as $order){
			$direction= ' asc';
			if (strpos($order,'>') !== false){ $direction= ' asc'; $order= strtr($order, array('>'=>'')); }
			if (strpos($order,'<') !== false){ $direction= ' desc'; $order= strtr($order, array('<'=>'')); }
			if (isset($this->fields[trim($order)]['expression'])){
				$field= $this->fields[trim($order)]['expression'];
				$orderExpression.= "$separator$field$direction";
				$separator= ', ';
			} //TODO ELSE LOG. 
		}
		return $orderExpression;
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
		
		if ($orderExpression= $this->getOrderExpression()) $query.= " order by $orderExpression";
		
		return $query; 
	}

// NOT EXISTING METHODS AND PROPERTIES
/*
	public void __set ( string $name , mixed $value )
	public mixed __get ( string $name )
	public mixed __call ( string $name , array $arguments )
*/
	
	
}
