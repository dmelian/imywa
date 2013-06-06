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
 * 
 */
class bas_sql_mybackup{
	public $result;
	public $success;
	public $errormsg;
	private $connection;
	
	public function __construct(){
		globaL $_SESSION;
		global $_LOG;
		
		$databasename = $_SESSION->apps[$_SESSION->currentApp]->mainDb;
		$hostname = $_SESSION->apps[$_SESSION->currentApp]->dbServer;
		
		
		
		$this->connection = mysqli_init();
		
		if (@$this->connection->real_connect($hostname, $_SESSION->user, $_SESSION->password, $databasename)){
			if (!$this->connection->autocommit(false)) $_LOG->log("mysql.exec.procedure_ext Error setting autocommit to false");
			$connection->set_charset('utf8');
			$this->success = true;
			
		} else {
			$this->errormsg = $connection->connect_error;
			$_LOG->log("mysql. error en conexión a host:$hostname, database:$databasename",3);
			$this->success = false;
		}
	}
	
	public function backuptable($table, $fields){
		
	}
	
	public function close(){
		if ($this->success) $this->connection->close;
	}
	
}
?>
