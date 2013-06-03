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


class bas_sqlx_rafdatapointer extends bas_sqlx_datapointer{
	const PACKHEADLENGTH= 12;

	private function MySQL ($query,$limit){ // cada uno debe crearse el suyo propio debido a la particularidad de bas_sql_myquery
	    
		global $_LOG;
		global $_SESSION; //bas_sysx_session;
		
		$_SESSION->apps[$_SESSION->currentApp]->getMainDb($host, $database);

		if (!isset($this->connect))$con = new mysqli($host, $_SESSION->user, $_SESSION->password, $database);
		else $con = $this->connect;
		if (mysqli_connect_errno())
		{
			$_LOG->log('Could not connect: ' . $con->error);
		}
		$con->set_charset('utf8');

		$result = $con->query($query);
		$this->current = array();
		$_LOG->log("Datapointer::Query: Consulta ejecutada: $query");
		if ($result != false){
			
		///////////////////////////////////////////////////////////////
			
/*	RANDOM ACCESS FILE FORMAT.
	HEADER
		record length
		record count
		field count
		fields names -> serialized null row data assoc
		first record file offset
		
	CONTENT
		record fields lengths and content
		
	For unserialize first we merge the null assoc with the fields content on a serialized string.
	afterwards, we unserialized the resulting string.
	
	How to serialized some objects in one only string?.
	1) Create an array and then serialize the array
	2) Managing the serializes length manually.
			
*/
			$maxLengths= 0;
			$fdNames= array();
			foreach (fetch_fields_direct() as $fdInfo){
				$fdNames[]= $fdInfo->name;
				$maxLengths+= $fdInfo->max_length +10; //type + separator + length(5) + separator + 2 string char terminators.
			}
			
			$header= array('fieldsCount' => $result->field_count);
			$header['fieldNames']= serialize($nullRecord);
			$header['recordSize']= maxLengths + strlen($header['fieldNames']);
			
			if ($row= $result->fetch_assoc()){
				
			}
			while($row = $result->fetch_row()) {
				$srow=serialize($row);
				
			}
			
			$_LOG->log("END >>>>>>>>>>>>>>>>");
			
			
			///////////////////////////////////////////////////////////////
			
			
			if ($limit == 1) $this->current= $result->fetch_assoc();
			else {
				$pos = 0;
				while($this->current[$pos++] = $result->fetch_assoc());
				unset($this->current[--$pos]);
				$this->size = $pos;
			}
			if (isset($this->pivot)) $this->pivotFormat();
			$this->original = $this->current;
		}
		else{
			$_LOG->log("DataPointer::Mysql. Se ha ha producido un error durante la consulta: {$con->error}");
			$_LOG->log("DataPointer::Mysql. Se ha ha producido un error en la query: $query");
		}
		if (!isset($this->connect))$con->close();
	}
	
	public function packArray($array, $serialize=true){
		$values= ''; $lengths= array();
		foreach($array as $value) {
			$svalue= $serialize ? serialize($value) : $value;
			$values.= $svalue;
			$lengths[]= strlen($svalue);
		}
		$result= implode(';',$lengths).":".$values; 
		return str_pad(strlen($result),PACKHEADLENGTH,'0',STR_PAD_LEFT).$result;
	}
	
	public function unpackArray($string, $unserialize=true){
		
	}
	
}

?>
