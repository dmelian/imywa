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
 * Ejecuta una query y retorna la primera línea como array asociativa
 *
 */
class bas_sql_myquery{
	public $result;
	public $success;
	public $errormsg;
	
	public function __construct($datadef, $databasename='', $hostname='', $connection=false){
		global $_SESSION;
		global $_LOG;
		
		if (is_object($datadef)) {$query = $datadef->query();}
		else $query = $datadef;

		//if (!$databasename) $databasename = $_SESSION->apps[$_SESSION->currentApp]->mainDb;
		//if (!$hostname) 	$hostname = $_SESSION->apps[$_SESSION->currentApp]->dbServer;
		
		if (($databasename === "")and($hostname===""))	$_SESSION->apps[$_SESSION->currentApp]->getMainDb($hostname, $databasename);
		if($hostname==="")$hostname="localhost";
		
		//TODO: Si es una tabla la trasnformamos en una select *.
		
		if ($connection === false) {
			$privateconnection = true;
			$connection = mysqli_init();
			if (@$connection->real_connect($hostname, $_SESSION->user, $_SESSION->password, $databasename)){
				$connection->set_charset('utf8');
				$connectionopened = true;
			} else {
				$this->errormsg = $connection->connect_error;
				$_LOG->log("myqrydataset.error en conexión a host:$hostname, database:$databasename",1);
				$this->success = false;
				$connectionopened = false;
			}
		} else {
			$privateconnection = false;
			$connectionopened = true;
		}
		
		if ($connectionopened){
			$this->success = @$connection->real_query($query);
			if ($this->success){
				$_LOG->log("myquery.success query: <$query>",10);
				if ($result = $connection->store_result()) {
					$this->result = $result->fetch_assoc();
					$result->close();
					if (is_null($this->result)) {
						$this->success = false;
						$this->errormsg = "No se han encontrado datos.";
					}
				} else {
					//Si no hay resultado es que se trata de una query de manipulación de datos.¿Deberíamos permitirla?
					if ($connection->error) $this->success = false;
				}
			} else {
				$this->errormsg = $connection->error;
				$_LOG->log("myquery.error $this->errormsg en query: <$query>",1);
			}
			if ($privateconnection) $connection->close();
		}
	}
	
}
?>
