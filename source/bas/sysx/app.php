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

class bas_sysx_app{
	public $appName;
	public $dbServer;
	public $dbs;
	/* assoc[id]
	 * id- database identificator used in source code.<parentDBid>(_<partitionId>)
	 * name- database name for this application. Is the real database name located in $this->dbServer.
	 * partitionId- database partition identifier ('' for parentDB)
	 * partitionLevel- integer identifying the level (begining in 1, 0 for parent or not partitioned DB) 
	 * currentPartitionValue- current selected partition
	 * partitionValues- 
	 */ 
	public $mainDb; // Identifier of the main database where the store procedures are loaded.
	public $role; // The role of the current user.
	public $permissions; //Permisions of the role for the current user.
	/* assoc[objective]
	 * objective - Module or form.
	 * actions - Optional comma separated actions ids, as multiple objective.
	 * permission - One of: (deny, allow, except (allow all except the actions listed), only (allow only the actions listed).
	 */
	public $defPermissionType; //Default permision Type: deny or allow.
	public $source;
	public $startClass;
	public $state;
	public $breadCrumb=array();
	
	private $appDir;
	
	public function __construct($app){
		
		$this->appName= $app['app'];
		$this->dbServer= $app['dbServer'];
		$this->source= $app['source'];
		$this->startClass= $app['startClass'];
		$this->theme= $app['theme'];
		$this->defPermissionType= $app['defPermissionType'];
		$this->role= isset($app['role']) ? $app['role']: 'user';
		$this->state= 'unloaded';
	}
	
	public function getMainDb(&$host, &$database){
		$host= $this->dbServer;
		$database= $this->getDbName($this->mainDb);
	}
	
	public function getDbName($dbId){
		
		if ($this->dbs[$dbId]['partitionLevel'] == 0) return $this->dbs[$dbId]['name'];	
		else {
			$partitions= explode('_', $dbId);
			$databaseId= array_shift($partitions);
			$database= $this->dbs[$databaseId]['name'];
			$i=0;
			foreach ($partitions as $partition){
				$databaseId.= "_$partition";
				$database.= "_{$this->dbs[$databaseId]['currentPartitionValue']}";
			}
			return $database; 
		}
	}
	
	public function changePartitionValue($db, $partitionValue){
		$this->dbs[$db]['currentPartitionValue']= $partitionValue;
	}
	
	public function serialize(){
		$fname = "{$this->appDir}/app";
		$fp = fopen($fname,'w');
		fwrite($fp, serialize($this));
		fclose($fp);
		chmod($fname, 0666);
	}
	
	
	
	public function OnPaintDashBoard(){ return "<p>{$this->appName}</p>"; }
	
	public function OnLoad(){
		global $_SESSION;
		global $_LOG;
		
		
		$con = new bas_sqlx_connection(true);
		if (!$con->success) {echo $con->getMessageBox()->jscommand(); return; }
		if (!$con->call('sessions','getAppInfo', array('app'=>$this->appName, 'role'=>$this->role))){
			echo $con->getMessageBox()->jscommand(); return;
		}

		$this->dbs= array();
		if ($appDbs= $con->getResult('dbs')) {
			foreach($appDbs as $db){
				$dbId= $db['partitionId']? "{$db['db']}_{$db['partitionId']}" : $db['db'];
				if (isset($db['dbName']))$aux = $db['dbName'];
				else $aux = $dbId;
				
				$this->dbs[$dbId]= array('id'=>$dbId
						, 'name'=>$aux
						, 'partitionId'=>$db['partitionId'], 'partitionLevel'=>$db['partitionLevel']
						, 'currentPartitionValue'=>''
						, 'partitionValues'=>array());
				if ($db['main']) $this->mainDb = $dbId;
			}
			$appDbs->close();
		}
		
		$this->permissions= array();
		if ($permissions= $con->getResult('permissions')){
			foreach($permissions as $permission){
				$this->permissions[$permission['objective']]= $permission;
			}
			$permissions->close();
		}
/*		
		$this->config= array();
		if ($configs= $con->getResult('config')){
			foreach($configs as $config) $this->config[$config['key']]=$config['value'];
			$configs->close();
		}
*/
		// The query to obtain the partition values must be done from the default database.
		foreach($this->dbs as $db){
			if ($db['partitionLevel']){
				//TODO: Modify this for partitions levels > 1.
				$parentDb= substr($db['id'],0,-strlen($db['partitionId'])-1);
				$enumPartitionsQry= "select partitionValue from $parentDb.{$db['partitionId']};";
				if ($con->query($enumPartitionsQry)){
					if ($ids= $con->getResult('last_query')) {
						foreach($ids as $id) {
							$this->dbs[$db['id']]['partitionValues'][]= $id['partitionValue'];
						}
						$this->dbs[$db['id']]['currentPartitionValue'] = 
						 	isset($this->dbs[$db['id']]['partitionValues'][0])
						 	? $this->dbs[$db['id']]['partitionValues'][0] : ''; 
						$ids->close();
					}
				}
			}
		}
		$con->close();
		
		$this->appDir= "{$_SESSION->sessionDir}/{$this->appName}";
		mkdir($this->appDir);
		chmod($this->appDir, 0777);
		mkdir("$this->appDir/forms");
		chmod("$this->appDir/forms", 0777);
		
		$this->state= 'loaded';
		return array('switch', $this->startClass);
		
	}
	
	public function classPermission($className){
		$permission= array('permission'=>$this->defPermissionType);
		
		$objective= $className;
		while($objective){
			if (isset($this->permissions[$objective])){
				$permission['permission']= $this->permissions[$objective]['permission'];
				if ($this->permissions[$objective]['actions']){
					$permission['actions']= $this->permissions[$objective]['actions'];
				}
				$objective= '';
				
			} else {
				$last_= strrpos($objective, '_');
				$objective= $last_ !== false ?  substr($objective, 0, $last_)  :  '';
			}
		}
		return $permission;
	}
	
	
	
// ----------------------------------------------------
// The Stack

	public function formpush(&$form){
		$caption= method_exists($form, "getBreadCrumbCaption")? $form->getBreadCrumbCaption() : '?';
		array_push($this->breadCrumb, $caption);
		$fname = "{$this->appDir}/forms/F" . str_pad(++$this->stacktop, 4, '0', STR_PAD_LEFT);
		$sessionFile= new syncFile($fname);
		if (!$sessionFile->setContent(serialize($form))) {
			$_LOG->log("Error: No se ha podido serializar el formulario ". get_class($form).".");
		}
		
	}
	
	public function formpop($jump=1){
		global $_LOG;
		//TODO: El último formulario de la pila no puede sacar. O si se saca se muestra el diagolo fin de la aplicación.
		$form= false;
		while ($jump > 0 && $this->stacktop > 0) {
			$fname = str_pad($this->stacktop--, 4, '0', STR_PAD_LEFT);
			if (--$jump == 0) {
				$formFile= new syncFile("$this->appDir/forms/F$fname");
				if ($formFile->getContent()) $form= unserialize($formFile->content);
				else $_LOG->log("Error: No se ha podido obtener el formulario de la pila.");
			}
			//unlink ("$this->appDir/forms/F$fname"); //TODO: problems with ajax parallels requests.
			array_pop($this->breadCrumb);
		}
		if ($form) return $form;
	}

/*TODO: FORMSEQUENCENO
	Al ser las llamadas ajax masivamente paralelas, no se pude seguir un número de secuencia.
	Vamos a poner un número de secuencia de formulario, para correguir los posibles errores de formulario.
	Si el número de formulario no es el mismo que se viene en el POST, muestro el formulario y no hago caso 
	a la acción.
*/	
	
	public function topform(){

		if ($this->stacktop > 0) {
			$fname = str_pad($this->stacktop, 4, '0', STR_PAD_LEFT);
			$formFile= new syncFile("$this->appDir/forms/F$fname");
			if ($formFile->getContent()) $form= unserialize($formFile->content);
			else $_LOG->log("Error: No se ha podido obtener el formulario del top de la pila.");
			return $form;
		}
	}
	
	public function formstackisempty(){
		return $this->stacktop == 0;
	}
	

	public function updatetopform(&$form){
		global $CONFIG;
		$fname = "$this->sessiondir/forms/F" . str_pad($this->stacktop, 4, '0', STR_PAD_LEFT);
		$fp = fopen($fname,'w');
		fwrite($fp, serialize($form));
		fclose($fp);
		chmod($fname, 0666);
	
	}
	
	public function getBreadCrumbCaptions(){
		return $this->breadCrumb;
	}
	
	
	
	
}
?>
