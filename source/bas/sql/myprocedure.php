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
 * Esta es la abtracción de un procedimiento almacenado.
 * Como Mysql no soporta la gestión de errores de usuario (los raise de errores)
 * hemos ideado la forma, devolviendo un conjunto de datos del procedimiento.
 * Las dos primeras columnas serán.
 * 0-error: Cierto si se ha detectado algún error de usuario. Los que produce mysql directamente se realizan por el conducto habitual.
 * 1-message: Mensaje de error 
 * 2... - de la columna 2 en adelante se trata de valores especificos de cada procedimiento.
 * 
 * Si se deseara mandar más datos que una sola fila se usaran tablas temporales de sesion, con el nombre del procedimiento.
 */
class bas_sql_myprocedure{
	public $result;
	public $success;
	public $errormsg;
	
	
	public function __construct($procedure, $params=array()){
		global $_SESSION;
		global $_LOG;
		$_SESSION->apps[$_SESSION->currentApp]->getMainDb($hostname, $databasename);
		
		$connection = mysqli_init();
		
		if (@$connection->real_connect($hostname, $_SESSION->user, $_SESSION->password, $databasename)){
			if (!$connection->autocommit(false)) $_LOG->log("mysql.exec.procedure Error setting autocommit to false");
			$connection->set_charset('utf8');
			$query = "call $procedure" . $this->expand($params);
			$_LOG->log("mysql.exec.procedure host:$hostname database:$databasename user:$_SESSION->user query:$query",3);
			$this->success = @$connection->real_query($query);
			if ($this->success){
				if ($result = $connection->use_result()){
					$this->result = $result->fetch_assoc();
					$result->close();
					extract($this->result, EXTR_PREFIX_ALL,'');
					eval ('$this->errormsg = "' . $this->result['message'] . '";');
					$this->success = !$this->result['error'];
				}
				while ($connection->next_result()) {
					// Si no se ejecuta el next_result() y el procedimiento devuelve un query el mysql se queda descolocado.
					// Solución aportada desde internet.
					if ($result = $connection->use_result()){ $result->close(); }
				}
				
				if ($this->success) {
					if (@$connection->commit()) $_LOG->log('mysql.exec.procedure success and commit.');
					else $_LOG->log("mysql.exec.procedure Error on commit: $connection->error.");
				} else {
					if (@$connection->rollback()) $_LOG->log('mysql.exec.procedure error and rollback.');
					else $_LOG->log("mysql.exec.procedure Error on rollback: $connection->error.");
				}
				
			} else {
				$this->errormsg = $connection->error;
				$_LOG->log("mysql.error $this->errormsg en query: <$query>",1);
				if ($end = @$connection->rollback()) $_LOG->log('mysql.exec.procedure error and rollback.');
				else $_LOG->log("mysql.exec.procedure Error on rollback: $connection->error.");
			}
				
			$connection->close();
			
		} else {
			$this->errormsg = $connection->connect_error;
			$_LOG->log("mysql. error en conexión a host:$hostname, database:$databasename",3);
			$this->success = false;
		}
		
		
	}
	
	private function expand($parameters){
		$parlist = ''; $prefix = '';
		foreach($parameters as $parameter) {
			if (strlen($parameter)==0 or is_null($parameter)){$parlist .=$prefix.'null';}  
			else {$parlist .="$prefix\"$parameter\"";} 
			$prefix = ', '; 
		}
		return "($parlist)";
	}
	
	
}
?>
