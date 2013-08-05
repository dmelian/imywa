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
class bas_sqlx_connection{
	// It is no possible to extends form mysqli, because of you can not declare class attributes on the child class
	public $success= false;
	public $results; //results of a procedure call or a query.
	public $return; //hash of the return of a procedure call
	
	protected $conn; //The mysqli connection
	
	protected $resultIds; //The identifiers of the nonanonymous results
	protected $host;
	protected $database;
	
	public $message;
	protected $closed= true;
	protected $unfinishedTransaction= false;
	
	
	//TODO: En el momento de crear la conección debe especificarse la aplicación para saber la base de datos a la que se conecta.
	//Si no se especifica nada, se conecta a la base de datos por defecto de imywa.
	/* La aplicación tiene su base de datos por defecto, que se llama igual que la aplicación
	 * , o que es la base de datos [0] de la aplicación, o se define una mainDB
	 * Cada base de datos puede tener particiones.
	 * Una partición de una base de datos es la separación de todas sus tablas en dos bases de datos. 
	 * En cada partición hay una base de datos padre que se queda con el mismo nombre y una base de datos hija que toma el nombre del padre 
	 * seguido de guión bajo y el identificador de la instancia de la partición.
	 * Este identificador de la partición se corresponde con una tabla de la base de datos padre.
	 * Esta tabla identificadora de la partición, contiene tantos registros como instancias de base de datos hijas existan.
	 * El identificador de la instancia hija es el campo 'name'.
	 * 
	 * Cada aplicación tiene un host de donde tira. Ya se habrá diseñado para que no tire de datos comunes con imywa.
	 */ 
	public function __construct($default=false){
		global $_SESSION;
		global $CONFIG;
		
		if (isset($_SESSION->currentApp) && !$default){
			$_SESSION->apps[$_SESSION->currentApp]->getMainDb($this->host, $this->database);
		} else {
			$this->host= $CONFIG['DEFAULTHOST'];
			$this->database= $CONFIG['DEFAULTDATABASE'];
		}
		$this->conn= mysqli_init(); 
		if (!$this->conn){ $this->setMessage('bas_sqlx_error', 'mysqliError'); return; }
		if (@$this->conn->real_connect($this->host, $_SESSION->user, $_SESSION->password, $this->database)) {
			$this->conn->autocommit(false);
			$this->conn->set_charset('utf8');
			$this->success= true;
			$this->closed= false;
			
		} else $this->setError();
        
    }

    protected function setMessage($module, $id, $texts= false){ $this->message= array('module'=>$module, 'id'=>$id, 'texts'=>$texts); }
    
    protected function setError(){
    	switch($this->conn->errno){
			case 2005: 	$this->setMessage('bas_sqlx_error', 'unknownHost', array('host'=>$this->host)); break; // connect_errno: 2005 - Unknown MySQL server host ...
			case 1044: 
			case 1045: 	$this->setMessage('bas_sqlx_error', 'accessDenied', array('user'=>$_SESSION->user)); break; // connect_errno: 1044 y 1045 - Access denied for user ...
			case 1049: 	$this->setMessage('bas_sqlx_error', 'unknownDatabase', array('database'=>$this->database)); break; // connect_errno: 1049 - User authenticated but database not found ...
			default:	
				$this->setMessage('bas_sqlx_error', 'dbGenericError', array('errno'=> $this->conn->errno, 'error'=> $this->conn->error)); //Any other error
		} 
    }
    
	public function getMessageBox(){
		return new bas_html_messageBox($this->message['module'], $this->message['id'], $this->message['texts']);
	}
	
	public function getMessage(){
		return $this->message['texts'];
	}
		
	public function commit(){
		if ($this->closed) return;
		if ($this->unfinishedTransaction) {
			$this->conn->commit(); 
			$this->unfinishedTransaction= false;
		}
	}
	
	public function rollback(){
		if ($this->closed) return;
		if ($this->unfinishedTransaction) {
			$this->conn->rollback(); 
			$this->unfinishedTransaction= false;
		}
	}

	protected function finishTransaction(){
		if ($this->closed) return;
		if ($this->unfinishedTransaction){
			if ($this->success) $this->commit();
			else $this->rollback();
			$this->unfinishedTransaction= false;
		}
	}
	
	public function close(){
		if ($this->closed) return;
		$this->finishTransaction();
		$this->conn->close();
		$this->closed= true;
	}

	public function __destruct(){
		$this->close();
	}
	
	public function call($module, $action, $params=array()){
		global $_SESSION;
		global $_LOG;
		
		if ($this->closed) return;
		$this->closeResults();
		
		if ($this->success){
			$query= "call {$module}_$action" . $this->expand($params);
			$_LOG->log("PROC> {$this->host}:{$this->database}:{$_SESSION->user} $query");
			$this->success= @$this->conn->real_query($query);
			if ($this->success){
				$this->unfinishedTransaction= true;
				unset($this->results); $this->results= array();
				while ($this->conn->more_results()){
					$this->results[]= new bas_sqlx_resultIterator($this->conn->store_result());
					$this->conn->next_result();
				}
				unset($this->resultIds);
				for($result=0; $result < count($this->results)-1; $result++){
					$firstRow=$this->results[$result]->current();
					if (isset($firstRow['resultId'])) {
						$this->resultIds[$firstRow['resultId']]= $result;
					}
				}
				$lastResult= count($this->results)-1;
				if ($lastResult >= 0){
					$this->return= $this->results[$lastResult]->current(); 
					$this->results[$lastResult]->close();
					unset($this->results[$lastResult]);

					// Application (Logical) error.
					if (isset($this->return['error'])) $this->success= $this->return['error'] == 0;
					
					if (isset($this->return['idmessage'])){
						if (!isset($this->return['idmodule'])){
							if ($endIdModule=strpos($this->return['idmessage'],'-')) {
								$this->return['idmodule']= substr($this->return['idmessage'],0,$endIdModule);
								$this->return['idmessage']= substr($this->return['idmessage'],$endIdModule+1);								
							} else $this->return['idmodule']= '';
						}
						$this->setMessage($this->return['idmodule'], $this->return['idmessage'], $this->return);
						//TODO: Revisar esto de nuevo porque no me convence.
						//DONE: Especificar el módulo correcto de donde sacar el texto.
						/* ¿ De donde vamos a sacar los ficheros de captions y descripciones de la base de datos. */
						/* Vamos a especificar un fichero por cada módulo de definición de la base de datos.
						 * Las particiones se crean como un módulo mas, por lo que al lenguaje se refiere.
						 * Los procedimientos son los que espeficican el módulo del lenguaje con el idmodule o 
						 * anteponiendolo en el idmessage con un guion medio */
						
						
					} elseif (isset($this->return['message'])) {
						extract($this->return, EXTR_PREFIX_ALL,'');
						eval ('$message = "' . $this->return['message'] . '";');
						$idmessage= $this->success ? 'defaultSuccess' : 'defaultError';
						$this->setMessage('bas_sqlx_error', $idmessage, compact('message'));
					}
				}

			} else {
				$_LOG->log("Error en la llamada a store procedure");
				$this->setError();
			}
		}
		return $this->success; 
	}
	
	public function query($query){
		global $_SESSION;
		global $_LOG;
		
		if ($this->closed) return;
		$this->closeResults();
		
		$sucess=false;
		if ($this->success){
			$success= @$this->conn->real_query($query);
			if ($success){
				unset($this->results); $this->results= array();
				$this->results[]= new bas_sqlx_resultIterator($this->conn->store_result());
				unset($this->resultIds);
				$this->resultIds= array('last_query'=> 0);

			} else {
				$_LOG->log("Error: en la query <<$query>>");
				$this->setError();
			}
		}
		return $success; 
	}
	
	
	public function getResult($resultId){
		if (isset($this->resultIds[$resultId])) return $this->results[$this->resultIds[$resultId]]; 
		else return array();
	}
	
	public function closeResults(){
		if (isset($this->results)){
			foreach($this->results as $result) $result->close();
			unset($this->results);
		}
	}

	protected function expand($userParams, $defParams=false){
		
		$parlist = ''; $prefix = '';
		if (! $userParams) $userParams = array();
		elseif (!is_array($userParams)) $userParams = explode(',',$userParams);
		
		if ($defParams){
			// The procedure's parameters have been defined		
			if ($defParams != 'void') foreach($defParams as $defParam) {
				if (isset($userParams[$defParam])) $parlist.= "$prefix\"{$userParam[$defParam]}\"";
				else $parlist.= $prefix . 'null';
				$prefix= ', ';
			}
			
		} else {
			foreach($userParams as $userParam) {
				if (strlen($userParam)==0 or is_null($userParam)) $parlist.= $prefix . 'null';  
				else $parlist.= "$prefix\"$userParam\""; 
				$prefix= ', '; 
			}
		}
		return "($parlist)";
	}
	
	
}
?>
