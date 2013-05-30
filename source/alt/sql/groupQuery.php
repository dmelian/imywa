<?php
class alt_sql_groupQuery extends alt_sql_query{

// FIELDS

	/* groupOption posible values
	 	group - Group by field
		(sum, avg, max, min, count) - Standard Aggregate Functions
		aggregate - aggregate expression.
	*/
	public function addField($name, $groupOption, $type, $table=''){
		$alias= $this->isAggregateFunction($groupOption)) ? "{$groupOption}_$name" : $name;
		$this->addFieldAs($name, $alias, $groupOption, $type, $table);
	}
	
	public function addFieldAs($name, $alias, $groupOption, $type, $table=''){		
		if (!$table) $table= $this->currTable;
		$expression= $this->isAggregateFunction($groupOption) ? "$groupOption($table.$name)" : "$table.$name"; 
		$this->addExpression($expression, $alias, $groupOption, $type);
		$this->fields[$alias]['table']= $table;
	}
	
	public function addExpression($expression, $alias, $groupOption, $type){
		$this->fields[$alias]= array('id'=>$alias, 'expression'=>$expression, 'groupOption'=>$groupOption, 'type'=>$type);
		$this->currField= $alias;
	}	
	
// GROUP
	private function isAggregateFunction($groupOption){
		return in_array($groupOption, array('sum','avg','max','min','count'));
	}
	
	public function getGroupExpression(){
		$groupExpression= $separator= '';
		foreach ($this->fields as $field) if($field['groupOption'] == 'group'){
			$groupExpression.= "$separator{$field['expression']}";
			$separator= ', ';
		}
		return $groupExpression;
		
	}
	
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
		
		if ($groupExpression= $this->getGroupExpression()) $query.= " group by $groupExpression";
		
		return $query; 
	}
	
	
	
}