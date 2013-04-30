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


class bas_sqlx_datapointer{
	public $query; // private o protected ¿?
	public $current;
	public $original;
	protected $pos_current, $size;
	protected $key;
	protected $fileName;	
	protected $connect;
	protected $pivot;			// Será un array asociativo donde pivot["pivot"] = campo pivotante y pivot["value"] = "campo contenedor del valor"
	
	public function __construct($queryObj){
	    $this->query = $queryObj;
	    $this->size = 0;  // ### Lo establecemos para la realización de las pruebas
	    $this->pos_current = 0;
	    
	    $pref = (get_called_class() == "bas_sqlx_record")? "RC":"DTV";
	    global $_SESSION; //bas_sysx_session;
	    $baseDir = $_SESSION->sessionDir."/temp/";		
		
	    $this->fileName = uniqid("",true);
	    while (file_exists($baseDir.$pref.$this->fileName)){	
			$this->fileName = uniqid("",true);
	    }
	    $file = fopen($baseDir.$pref.$this->fileName, 'c');   
	    fclose($file);	    
	    $this->fileName = $baseDir.$pref.$this->fileName;
	    
	    
	}
	public function setConnection($con){
		$this->connect = $con;
	}
	// Se genera la clausula SQL para los movimientos extremos (FIRST y LAST).
	private function createQueryPos($limit,$pos){
	
	    $query = "";
		$query = $query . $this->query->query(true);
	    if ($limit != -1)  $query = $query . " limit ". $pos.", ".$limit.";";
	    return $query;	
	}
	
	public function first_pos($limit){	    
	    $this->MySQL($this->createQueryPos($limit,0),$limit);
	    $this->pos_current = 0;
	}
	
	public function last_pos($limit){	    
	   /* $this->MySQL($this->createQueryPos($limit,$this->size-$limit),$limit); //antes true
	    $this->pos_current = $this->size -$limit;*/
	    $pos = $this->size - ($limit % $this->size);
	    if ($pos >= 0){
		$this->MySQL($this->createQueryPos($limit,$pos),$limit); 
		$this->pos_current = $pos;
	    }
	}
	
	public function prox_next($limit,$pos=""){
		if ($pos === ""){
			$pos = $this->pos_current + $limit;
		}	    
	    if ($pos<$this->size){
			$this->MySQL($this->createQueryPos($limit,$pos),$limit);
			$this->pos_current = $pos;
	    }
	}
	
	public function prox_previous($limit,$pos=""){
	    if ($pos === ""){
			$pos = $this->pos_current - $limit;
		}
		
	    if ($pos > -1){
			$this->MySQL($this->createQueryPos($limit,$pos),$limit); // antes true
			$this->pos_current = $pos;
	    }
	}
	
	
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
			if ($limit ==1){
				while($row = $result->fetch_assoc())
				{
					foreach($row as $field => $value){
						$this->current[$field] = $value;
					}
				}
			}
			else{
				$pos = 0;
				while($row = $result->fetch_assoc())
				{
					foreach($row as $field => $value){
						$this->current[$pos][$field] = $value;
					}
				$pos++;
				} 	
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
	public function setPivot($pivot,$value){
		$this->pivot = array("pivot"=>$pivot, "value"=>$value);
	}
	protected function pivotFormat(){
		$container = array();
		$pos = 0;
		foreach($this->current as $row){
			foreach($row as $field => $value){
				if (($field != $this->pivot["pivot"])  && ($field != $this->pivot["value"])){ // nos enctramos con un campo que no pertenece a los campos pivot ni valor.
					if (isset($container[$pos][$field])){
						if ($container[$pos][$field] != $value){  // hemos encontrado un campo distinto al anterior, por lo que se ha completado este registro.
							$pos++;
							$container[$pos][$field] = $value;
						}
					}
					else
						$container[$pos][$field] = $value;
				}
				else{
					if ($field == $this->pivot["pivot"]){
						$container[$pos][$value] = $row[$this->pivot["value"]];
					}
				}
					
			}
		}
		$this->current = $container;
	}
	

/*
  ############################################################################################################################################
		      Acceso a los registros mediante el uso de ficheros.
  ############################################################################################################################################
*/
	protected function acces_posfile($pos,$limit){ 
	// TO-DO: Gestionar la posible inexistencia del fichero. Desidir el nombre y ubicación final
	    $vector = unserialize(file_get_contents($this->fileName));
// 	    global $_LOG;
	    if (!isset($vector[$pos])) return NULL;
	    
		if ($pos <= $this->size){

			if ($limit == 1) return $vector[$pos];
			
			$register = "";
			if (($pos+$limit) > $this->size){
				$limit = $limit - ( ($pos+$limit) - $this->size);
			}
			for($index=$pos,$nelem=0;$nelem <$limit; $index++, $nelem++){ // Optimizable
				$register[$nelem] = $vector[$index];
			}
			return $register;
		}
		else{
			global $_LOG;
			$_LOG->log(get_class($this)."::acces_posfile. Se ha sobrepasado el maximo {$this->size}");
			return array();
		}
	}
	
	protected function save_data(){ // almacenamos la informacion obtenida en fichero
	    global $_LOG;
	    $file = fopen($this->fileName, 'w');
	    if (!$file) $_LOG->log("Error durante la apertura de fichero. DataPointer::save_data");
		$this->size = count($this->current);
	    $data = serialize($this->current);

	    if (fwrite($file,$data)===false) {
			$_LOG->log("Error durante la escritura. DataPointer::save_data");
	    }
	    fclose($file);
	}
	
	public function load_data(){
	    $this->MySQL($this->createQueryPos(-1,0),-1);
	    $this->save_data();
	}
	
	public function first_file($limit){
	    $this->data_posfile(0,$limit);
	}
	
	public function last_file($limit){
	    $pos = $this->size - ($limit % $this->size);
	    $this->data_posfile($pos,$limit);	
	}
	
	public function next_file($limit){
	    $pos = $this->pos_current + $limit;
	    $this->data_posfile($pos,$limit);
	}
	
	public function previous_file($limit){
	    $pos = $this->pos_current - $limit;
	    $this->data_posfile($pos,$limit);
	}
	
	protected function data_posfile($pos,$limit){
		if (  ($pos<$this->size)  &&  ($pos>=0)  ){			
			$rows= $this->acces_posfile($pos,$limit);
			if (isset($rows)){
				$this->current = $rows;
				$this->original = $rows;
				$this->pos_current = $pos;
			}
	    }
	}
	
	protected function get_dataRow($pos){
		if ($pos<$this->size){
			$row = $this->acces_posfile($pos,1);
			if (isset($row))return $row;
	    }
	    return NULL;
	}
	
	public function initRecord(){
		$this->current= $this->query->getemptyrec();
		$this->original= $this->current;
	}
}

?>
