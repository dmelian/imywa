<?php
class alt_sql_groupQuery extends alt_sql_query{

// FIELDS

	/* groupOption posible values
	 	group - Group by field
		(sum, avg, max, min, count) - Standard Aggregate Functions
		aggregate - aggregate expression.
	*/
	public function addField($name, $groupOption, $type, $table=''){
		$alias= $this->isAggregateFunction($groupOption) ? "{$groupOption}_$name" : $name;
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

// FILTERS
	
	#groupOption for filters group | aggregate
	public function addFieldFilter($field, $filter){
		if (isset($this->fields[$field])) {
			$groupOption= $this->fields[$field]['groupOption'] == 'group' ? 'group' : 'aggregate';
			$this->addFilterAs($this->fields[$field]['expression'], $field, $groupOption,$filter);
		} //TODO ELSE LOG
	}
	
	public function addFilter($expression, $groupOption, $filter){ $this->addFilterAs($expression, '', $groupOption, $filter); }
	
	public function addFilterAs($expression, $id, $groupOption, $filter){
		if ($id) $this->filters[$id]= array('leftPart'=>$expression, 'groupOption'=>$groupOption, 'filter'=>$filter, 'id'=>$id);
		else $this->filters[]= array('leftPart'=>$expression, 'groupOption'=>$groupOption, 'filter'=>$filter);
	}
	
	public function getWhereExpression(){
		$whereExpression= ''; $logicOperator= '';
		$parser= new alt_sql_filterParser();
		foreach($this->filters as $filter){
			if ($filter['groupOption'] == 'group' && $filterExpression= $parser->parseFilter($filter['leftPart'], $filter['filter'])){
				$whereExpression.= "$logicOperator( $filterExpression )";
				$logicOperator= " {$this->filterMode} ";
			}
		}
		return $whereExpression;
	}
	
	private function getHavingExpression(){
		$havingExpression= ''; $logicOperator= '';
		$parser= new alt_sql_filterParser();
		foreach($this->filters as $filter){
			if ($filter['groupOption'] == 'aggregate' && $filterExpression= $parser->parseFilter($filter['leftPart'], $filter['filter'])){
				$havingExpression.= "$logicOperator( $filterExpression )";
				$logicOperator= " {$this->filterMode} ";
			}
		}
		return $havingExpression;
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
		
		if ($groupExpression= $this->getGroupExpression()) $query.= " group by $groupExpression";
		
		if ($havingExpression= $this->getHavingExpression()) $query.= " having $havingExpression";
		
		if ($orderExpression= $this->getOrderExpression()) $query.= " order by $orderExpression";
		
		return $query; 
	}
	
	
	
}