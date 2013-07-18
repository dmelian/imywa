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
	protected $currentPos, $size;
	protected $key;
	protected $fileName;	
	protected $connect;
	protected $pivot;			// Será un array asociativo donde pivot["pivot"] = campo pivotante y pivot["value"] = "campo contenedor del valor"
	
	protected $offset_PosFile=0;
	protected $maxSize_Record=0;
	protected $sizeMax_record=0;
	
	public function __construct($queryObj){
	    $this->query = $queryObj;
	    $this->size = 0;  // ### Lo establecemos para la realización de las pruebas
	    $this->currentPos = 0;
	    
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
	    $this->currentPos = 0;
	}
	
	public function last_pos($limit){	    
	   /* $this->MySQL($this->createQueryPos($limit,$this->size-$limit),$limit); //antes true
	    $this->currentPos = $this->size -$limit;*/
	    $pos = $this->size - ($limit % $this->size);
	    if ($pos >= 0){
		$this->MySQL($this->createQueryPos($limit,$pos),$limit); 
		$this->currentPos = $pos;
	    }
	}
	
	public function prox_next($limit,$pos=""){
		if ($pos === ""){
			$pos = $this->currentPos + $limit;
		}	    
	    if ($pos<$this->size){
			$this->MySQL($this->createQueryPos($limit,$pos),$limit);
			$this->currentPos = $pos;
	    }
	}
	
	public function prox_previous($limit,$pos=""){
	    if ($pos === ""){
			$pos = $this->currentPos - $limit;
		}
		
	    if ($pos > -1){
			$this->MySQL($this->createQueryPos($limit,$pos),$limit); // antes true
			$this->currentPos = $pos;
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
		@$con->set_charset('utf8');

		@$result = $con->query($query);
		$this->current = array();
		$_LOG->log("Datapointer::Query: Consulta ejecutada: $query");
		if ($result != false){
			if ($limit == 1) $this->current= $result->fetch_assoc();
			else {
				$pos = 0;
				while($this->current[$pos++] = $result->fetch_assoc());
				unset($this->current[--$pos]);
				$this->size = $pos;
			}
			if (isset($this->pivot)) $this->pivotFormat();
			$this->original = $this->current;
		} else {
			$_LOG->log("(bas_sqlx_datapointer)\nconsulta: $query\nerror: {$con->error}", 1, 'sql_errors');
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
	private function get_line(&$file){
		return stream_get_line ( $file, 99999,'\0\n');
	}
	
	protected function acces_posfile($pos,$limit){
		global $_LOG;
// 	    $_LOG->debug("######### 		Comienzo del acces_posfile pos:$pos limit:$limit. CurrentPos:{$this->currentPos}		 #########",array());
	    if (($pos >= 0) && ($pos <= $this->size)){
			$register = array();
			$file = fopen("/usr/local/imywa/temp/serialize.data","r");
			
			if ($this->currentPos <= $pos ){ // positive access.
				
				$this->fseek($file,$this->offset_PosFile); // ### Controlar el acceso correcto.
				$numPos = $pos - $this->currentPos;
				for($ind=0;$ind < $numPos; $ind++) $this->get_line($file);
				$this->offset_PosFile = ftell($file);
				$this->currentPos = $pos;
				
// 				$_LOG->debug("######### 		Acceso Positivo limnit:$limit. numPos=$numPos. offset_PosFile={$this->offset_PosFile}. currentPos={$this->currentPos}  #########",array());
			}
			else{ // negative access.
			// ### Posible mejora: Para no realizar una relectura de los siguentes elementos a la nueva posicion. Podríamos saltarlos en la lectura final.
// 				$_LOG->debug("######### 		Acceso Negativo   #########",array());
				$gapPos = $this->currentPos - $pos;
				$offset = $this->offset_PosFile - $this->sizeMax_record*4*($gapPos+1);
				if ($offset < 0) $offset = 0;
				$this->fseek($file,$offset);
				if ($offset != 0)$this->get_line($file);
				
				$array_offset = array();
				$array_offset[]  = ftell($file);
				while ( (ftell($file) != $this->offset_PosFile)  and ( !feof($file) )  ) { 
					$this->get_line($file);
					$array_offset[]  = ftell($file);
				}
// 				$_LOG->debug("PosAct:: {$this->currentPos} OffsetCur::{$this->offset_PosFile} OFFSET incial:: $offset Posicion $pos::GAP $gapPos:: INDICE::".(count($array_offset)-$gapPos),$array_offset);
				$this->offset_PosFile = $array_offset[count($array_offset)-$gapPos-1]; 
				$this->currentPos = $pos;
				$this->fseek($file,$this->offset_PosFile);
			}
			
// 			for($ind=0;( ($ind < $limit) and (!feof($file)) ); $ind++) {
// 				$contenido = $this->get_line($file);
// 				$_LOG->debug("Valor unserialize:: ",$contenido);
// 				$register[$ind] =unserialize($contenido);
// 			}
			
			for($ind=0;($ind < $limit); $ind++) {
				$contenido = $this->get_line($file);
// 				$_LOG->debug("Valor unserialize:: ",$contenido);
				$register[$ind] =unserialize($contenido);
				if (feof($file)) break;
			}
			
// 			if (!feof($file))$_LOG->debug("Fin de fichero!!",array());
			
			fclose($file);
			return $register;
		}
// 		$_LOG->log(get_class($this)."::acces_posfile. Se ha sobrepasado el maximo {$this->size}");
		return array();
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
	    
	    $file = fopen("/usr/local/imywa/temp/serialize.data","w");
		foreach($this->current as $index => $row){
			$content = serialize($row);
			fwrite($file,serialize($row)."\0\n");
			$size = strlen($content);
			if ($size > $this->sizeMax_record)$this->sizeMax_record=$size;
		}
// 		$_LOG->debug("Tamaño max: {$this->sizeMax_record}",array());
		fclose($file);
	}
	
	public function load_data(){
	    $this->MySQL($this->createQueryPos(-1,0),-1);
	    $this->save_data();
	}
	
	protected function fseek($file,$offset){
		usleep(20000);
		fseek($file,$offset);
	}
	
	public function first_file($limit){
	    $this->data_posfile(0,$limit);
	}
	
	public function last_file($limit){
	    $pos = $this->size - ($limit % $this->size);
	    $this->data_posfile($pos,$limit);	
	}
	
	public function next_file($limit){
	    $pos = $this->currentPos + $limit;
	    $this->data_posfile($pos,$limit);
	}
	
	public function previous_file($limit){
	    $pos = $this->currentPos - $limit;
	    $this->data_posfile($pos,$limit);
	}
	
	protected function data_posfile($pos,$limit){
		if (  ($pos<$this->size)  &&  ($pos>=0)  ){			
			$rows= $this->acces_posfile($pos,$limit);
			if (isset($rows)){
				$this->current = $rows;
				$this->original = $rows;
				$this->currentPos = $pos;
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
