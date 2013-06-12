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
 * Partiendo el bas_sql_myprocedure esta es la abstracción para la ejecución de multiples
 * procedimientos en una sola transacción.
 * 
 * El sacamos del constructor la ejecución del procedimiento, quedando el constructor como
 * un simil del begin transaction.
 * 
 * Implementamos el método call que es el que ejecuta en sí el procedimiento.
 * El exito o fracaso de cada procedimiento individual lo tenemos en la variable success.
 * 
 * Para terminar el proceso o cerramos guardando todos los cambios o abortamos los cambios hechos.
 * Si alguno de los procedimientos devuelve error se aborta el procedimiento completo.
 * 
 */
class bas_sql_myextprocedure{
	public $result;
	public $success;
	public $errormsg;
	public $connection;
	private $rolledback;
	private $rollbackatclose;
	public $errorCode;
	
	public function __construct($database=''){
		global $_SESSION;
		global $_LOG;
		
		$_SESSION->apps[$_SESSION->currentApp]->getMainDb($hostname, $databasename);
		//$databasename = $_SESSION->apps[$_SESSION->currentApp]->mainDb;
		//$hostname = $_SESSION->apps[$_SESSION->currentApp]->dbServer;
		if($database != '') $databasename = $database;
		$this->connection = mysqli_init();
		
		$this->rolledback = false;
		$this->rollbackatclose = false;
		
		if (@$this->connection->real_connect($hostname, $_SESSION->user, $_SESSION->password, $databasename)){
			if (!$this->connection->autocommit(false)) $_LOG->log("mysql.exec.procedure_ext Error setting autocommit to false");
			$this->connection->set_charset('utf8');
			$this->success = true;
			
		} else {
			$this->errormsg = $this->connection->connect_error;
			$_LOG->log("mysql. error en conexión a host:$hostname, database:$databasename",3);
			$this->success = false;
		}
	}
	
	public function call($procedure, $params=array(),$database=''){
		global $_SESSION;
		global $_LOG;
		
		$databasename = $_SESSION->apps[$_SESSION->currentApp]->mainDb;
		$hostname = $_SESSION->apps[$_SESSION->currentApp]->dbServer;
        if($database != '') $databasename = $database;

		if ($this->success){
			$query = "call $procedure" . $this->expand($params);
			$_LOG->log("mysql.exec.procedure_ext host:$hostname database:$databasename user:$_SESSION->user query:$query",3);
			$this->success = @$this->connection->real_query($query);
			if ($this->success){
				if ($result = $this->connection->use_result()){
					$this->result = $result->fetch_assoc();
					$result->close();
					extract($this->result, EXTR_PREFIX_ALL,'');
					if (isset($this->result['message'])) eval ('$this->errormsg = "' . $this->result['message'] . '";');
					else $this->errormsg = "Error {$this->result['error']}.";
					$this->success = !$this->result['error'];
					$this->errorCode = $this->result['error'];
					if (isset($this->result['rollbackatclose'])) $this->rollbackatclose = $this->result['rollbackatclose']; 
				}
				while ($this->connection->next_result()) {
					// Si no se ejecuta el next_result() y el procedimiento devuelve un query el mysql se queda descolocado.
					// Solución aportada desde internet.
					if ($result = $this->connection->use_result()){ $result->close(); }
				}
				if (!$this->success) {
					if ($this->rollbackatclose){
						$_LOG->log("'mysql.exec.procedure_ext error with rollback at close.'");
					} else {
						if (@$this->connection->rollback()) $_LOG->log('mysql.exec.procedure_ext error and rollback.');
						else $_LOG->log("mysql.exec.procedure_ext Error on rollback: {$this->connection->error}.");
						$this->connection->close();
					}
				}
				
			} else {
				$this->errormsg = $this->connection->error;
				$this->errorCode = $connection->connect_errno;				
				$_LOG->log("mysql.error $this->errormsg en query: <$query>",1);
				if ($end = @$this->connection->rollback()) $_LOG->log('mysql.exec.procedure_ext error and rollback.');
				else $_LOG->log("mysql.exec.procedure_ext Error on rollback: {$this->connection->error}.");
				$this->connection->close();
			}
		} 
	}
	
	public function query($query, $params=array()){
		global $_SESSION;
		global $_LOG;
		
		$databasename = $_SESSION->apps[$_SESSION->currentApp]->mainDb;
		$hostname = $_SESSION->apps[$_SESSION->currentApp]->dbServer;

		if ($this->success){
			$query = $this->expand_query($query, $params);
			if ($query) {
				$_LOG->log("mysql.exec.procedure_ext host:$hostname database:$databasename user:$_SESSION->user query:$query",3);
				$this->success = @$this->connection->real_query($query);
				if ($this->success){
					if ($result = $this->connection->use_result()){
						$this->result = $result->fetch_assoc();
						$result->close();
					}
				} else $this->errormsg = $this->connection->error;
			} else {
				$values = ''; foreach($params as $key => $value) $values +=" $key=>$value";
				$this->errormsg = "Error al expandir \"$query\" con valores$values.";
				$this->success = false;
			}
				
			if (!$this->success){
				$_LOG->log("mysql.error $this->errormsg en query: <$query>",1);
				if ($end = @$this->connection->rollback()) $_LOG->log('mysql.exec.procedure_ext error and rollback.');
				else $_LOG->log("mysql.exec.procedure_ext Error on rollback: {$this->connection->error}.");
				$this->connection->close();
			}
		}
	}
	
	public function commit(){
		global $_LOG;
		if ($this->success) {
			if (@$this->connection->commit()) $_LOG->log('mysql.exec.procedure_ext success and commit.');
			else $_LOG->log("mysql.exec.procedure_ext Error on commit: ".$this->connection->error);
			$this->connection->close();
		}
	}
	
	public function rollbackandclose(){
		global $_LOG;
		if ($this->rollbackatclose){
			if (@$this->connection->rollback()) $_LOG->log('mysql.exec.procedure_ext close and rollback.');
			else $_LOG->log("mysql.exec.procedure_ext Error on rollback at close: $this->connection->error.");
			$this->connection->close();
		}
	}
	
	public function cancel(){
		global $_LOG;
		if ($this->success) {
			if (@$this->connection->rollback()) $_LOG->log('mysql.exec.procedure_ext cancel and rollback.');
			else $_LOG->log("mysql.exec.procedure_ext Error on rollback at cancel: $this->connection->error.");
			$this->connection->close();
		}
	}
	
	private function expand($parameters){
		$parlist = ''; $prefix = '';
		if (! $parameters) $parameters = array();
		elseif (!is_array($parameters)) $parameters = explode(',',$parameters);
		foreach($parameters as $parameter) {
			if (strlen($parameter)==0 or is_null($parameter)){$parlist .=$prefix.'null';}  
			else {$parlist .="$prefix\"$parameter\"";} 
			$prefix = ', '; 
		}
		return "($parlist)";
	}
	
	private function expand_query($qry, $params){
		if (count($params) == 0) return $qry;
		else {
			extract($params,EXTR_PREFIX_ALL,'');
			if (eval("\$ret = \"$qry\";") !== false) return $ret;
			else return false; 
		}
	}
}
?>
