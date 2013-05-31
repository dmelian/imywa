<?php

class bas_sysx_jsCommands{
	public $commands;
	
	public function __construct(){ 
		ob_start();
		$this->commands= array();	
	}

	public function nextjsCommand(){
		if ($command= ob_get_contents()) $this->commands[]= $command; 
		ob_clean();
		
	}
	
	public function close(){
		if ($command= ob_get_contents()) $this->commands[]= $command; 
		ob_end_clean();
	}

	public function getjsCommands(){
		switch (count($this->commands)){
			case 0: return '{"command":"void"}';
			case 1: return $this->commands[0];
			default:
				$ret=''; $sep='';
				foreach($this->commands as $command) { $ret.= "$sep$command"; $sep=','; }
				return "{\"command\":\"compound\",\"commands\":[$ret]}";
		}
	}
	
}


class bas_sysx_session{
	public $sessionId;
	public $sessionDir;
	public $language;
	public $theme;
	public $currentApp;
	
	public $user;
	public $password; //TODO: encrypt it. 
	
	public $apps;
	
	private $currentForm;
	private $paintDashboard;
	
	public function __construct(){
		global $CONFIG;
		$this->language= $CONFIG['DEFAULTLANGUAGE'];
		$this->theme= $CONFIG['DEFAULTTHEME'];
		$this->apps= array();
	}

// Initialization

	public function serialize(){
		global $_LOG;
		// the current application source file isn't used currently. 
		//$fname= "{$this->sessionDir}/cas";
		//file_put_contents($fname, $this->apps[$this->currentApp]->source);
		//chmod($fname, 0666);
		$fname = "{$this->sessionDir}/session";
		$sessionFile= new syncFile($fname);
		if (!$sessionFile->setContent(serialize($this))) {
			//TODO: LOG $sessionFile->errormsg Mensaje 'No se ha podido guardar su sesion'
			$_LOG->log("Error: No se ha podido serializar la session {$this->sessinoid}.");
		}
	}
	
	
	public function begin(){
		
		$this->currentForm= new bas_sysx_begin();
		if (isset($_POST['action'])){
			
			$eventName= 'On'.ucfirst($_POST['action']);
			if (method_exists($this->currentForm, $eventName)){
				$command= call_user_func(array($this->currentForm, $eventName), $_POST);
			} else $command= $this->currentForm->OnAction($_POST['action'], $_POST);

			if ($command) $this->execCommand($command);
			
		} else {
			$this->currentForm->OnLoad();
			$this->currentForm->OnPaint();
		} 
	}
	
	public function wakeUp(){
	global $_LOG;
		if (isset($_POST["SessionAction"])){
			$this->execCommand(array($_POST["SessionAction"],$_POST));
			
		} else {
			$this->currentForm = $this->apps[$this->currentApp]->formpop();
			$jscommands= new bas_sysx_jsCommands();
			$sescmd = $this->currentForm->OnAction(isset($_POST['action']) ? $_POST['action'] : 'undefined',$_REQUEST);
			$jscommands->nextjsCommand();
			if ($sescmd) $this->execcommand($sescmd);
			$jscommands->close();
			echo $jscommands->getjsCommands();
		}
		//TODO: Si no se ha printado nada, enviar un void command para no dejar peticiones abiertas en el navigador. 
		$this->apps[$this->currentApp]->formpush($this->currentForm);
		$this->serialize();
	}
	
// Forms return commands.
	
	private function execCommand($command){
		global $_LOG;
		
		$commandName= 'cmd_'.array_shift($command);

		if (method_exists($this, $commandName)){
			call_user_func_array(array($this, $commandName), $command);
		} else {
			//$this->undefinedCommand($commandName, $command);
			$_LOG->log("bas_sysx_function undefined session command '$commandName'.");
		}
				
	}
	
	
	public function cmd_createSession($user, $password){
		global $CONFIG;
		global $_LOG;

		//$_LOG->log('Creating Session');
		// User authenticate.
		$this->user= $user;	$this->password= $password; 
		$db = new bas_sqlx_connection();
		if (!$db->success) {echo $db->getMessageBox()->jscommand(); return; }
			
		// Create the session folder
		$sessionbasedir = "${CONFIG['BASDIR']}run/sessions/";
		if (!file_exists($sessionbasedir)){
			mkdir($sessionbasedir); chmod($sessionbasedir, 0777);
		}

		$attemps= 0; $sessionId= '';
		do {
			$sessionId= uniqid('X');
			$this->sessionDir= "$sessionbasedir{$sessionId}";
			if (!file_exists($this->sessionDir)){
				mkdir($this->sessionDir); 			chmod($this->sessionDir, 0777); 
				mkdir($this->sessionDir."/temp"); 	chmod($this->sessionDir."/temp", 0777); 

			} 
			else  $sessionId= '';
		} while (!$sessionId && ($attemps++ < 5));
		if ($sessionId) $this->sessionId= $sessionId;
		
		// Load the imywa app.

		// Initialize the new session
		//$db->defProc('sessions', array('insert' => 'user,sessionno'));
		if (!$db->call('sessions','create', compact('sessionId'))){
			echo $db->getMessageBox()->jscommand(); return;
		}
		
		
		//TODO: session-create devuelve la aplicaciones que se crean, el rol para la aplicación y a cual se le dá el control.
		
		//TODO: unhandwrite this.
		$this->apps= array();
		if ($sessionApps= $db->getResult('sessionApps')) {
				
			foreach($sessionApps as $app){
				//require_once("{$CONFIG['BASDIR']}source/apps/{$app['source']}/app.php");
				$appclassname= "{$app['source']}_app";
				if (class_exists($appclassname)) $this->apps[$app['app']]= new $appclassname($app);
				else $_LOG->log("Error: Error al carga la aplicación {$app['app']}. No se encuentra la clase $appclassname.");
			}
			$sessionApps->close();
		}
		
		if ($userConfigs= $db->getResult('userConfig')){
			$userConfig= $userConfigs->current();
			$this->currentApp=  $userConfig['defApp'];
			$userConfigs->close();
		}
		
		$db->close();		
		
		$this->paintDashboard= true;
		$command= $this->apps[$this->currentApp]->OnLoad();
		if ($command) $this->execCommand($command);
		$this->apps[$this->currentApp]->formpush($this->currentForm);
		$this->serialize();
		
		
		
	}
	
	public function cmd_changeApp($data){
		$app= $data['app'];
		
		if (!isset($this->apps[$app])){ // la aplicación a tratar no se encuentra o no forma parte del conjunto que posee el usuario
			echo "{\"command\": \"alert\", \"message\": \"Desconocida la apliación $app\"}"; 
			return; 
		}
		
		if ($this->currentApp != $app){
			$this->currentApp = $app;
			
			if ($this->apps[$app]->state == "unloaded"){ // Si no ha sido cargada aún realizamos el protocolo de arranque.
				$this->paintDashboard= true;
				$command= $this->apps[$this->currentApp]->OnLoad();
				if ($command) $this->execCommand($command);
				
			} else { // la aplicación ya ha sido cargada.
				$this->currentForm = $this->apps[$this->currentApp]->formpop();
				$dash = $this->createDashboard();
				$html = addcslashes($dash,"\t\"\n\r");			
				$this->currentForm->OnPaint('jscommand',$html);
			}
			
		} else echo "{\"command\": \"void\"}"; 
	}
	
	public function cmd_changePartition($data){
		$this->currentForm = $this->apps[$this->currentApp]->formpop();
		$this->apps[$this->currentApp]->changePartitionValue($data['db'], $data['partitionId']);
		echo "{\"command\":\"alert\", \"message\":\"Cambiamos a la partición: {$data['partitionId']}\"}";
		//TODO: Cargar el formulario inicial de la aplicación.
	}
	
	
	private function createDashboard($reload=false){
		$html='';
		foreach ($this->apps as $id => $app){
			$html .= '<div id="'.$id.'" class="group';
			if ($this->currentApp == $id) $html .= ' selected_dash_first';
			$html .= '"><h3 class="dash_element">'.$app->appName.'</h3>'
				.'<div>';
			if ($app->state != 'unloaded') $html.= $app->onPaintDashboard();
			$html.= '</div></div>';
		}
		if (!$reload)	$html = '<div id="accordion">'.$html.'</div>';
		return $html;
	}
	
	public function cmd_open($class, $actions=array(), $singledata=null){
		$this->apps[$this->currentApp]->formpush($this->currentForm);
		if (!is_array($actions)){
			$actions= array(array('action'=>$actions, 'data'=> $singledata));
		}
		$this->cmd_switch($class, $actions);
	}
	
	public function cmd_switch($class, $actions=array(), $singledata=null){
		$permission= $this->apps[$this->currentApp]->classPermission($class); 		
		if ($permission['permission'] != 'deny'){

			$this->currentForm = new $class;
			$this->currentForm->sessionId= $this->sessionId;
			if (!is_array($actions)){
				$actions= array(array('action'=>$actions, 'data'=> $singledata));
			}
			if (method_exists($this->currentForm, 'OnLoad')) $this->currentForm->OnLoad($permission);
			foreach ($actions as $action){
				$this->currentForm->OnAction($action['action'], $action['data']);
			}
			if ($this->paintDashboard){
				$this->paintDashboard = false;
				$dash = $this->createDashboard();
				$html = addcslashes($dash,"\t\"\n\r");			
				$this->currentForm->OnPaint('jscommand',$html);
			}
			else $this->currentForm->OnPaint('jscommand');
			
		} else {
			echo "{\"command\": \"alert\", \"message\": \"No tienes permiso para ejecutar el formulario $class\"}"; 
			
		}		
	}
	
	public function cmd_refreshDashboard(){
		$dash = $this->createDashboard(true);
		$html = addcslashes($dash,"\t\"\n\r");			
		echo "{\"command\":\"refreshDashboard\",\"content\":\"$html\"}";
		
	}
	
	public function cmd_close($jump=1){
		if ($jump > 0){
			$this->currentForm = $this->apps[$this->currentApp]->formpop($jump);
			if (method_exists($this->currentForm, 'OnRefresh')) $this->currentForm->OnRefresh();
			$this->currentForm->OnPaint('jscommand');
		}
	}
	
	public function cmd_return($actions, $singledata=null){
		global $_LOG;
		if (!is_array($actions)) $actions= array(array('action'=>$actions, 'data'=> $singledata));
		$this->currentForm = $this->apps[$this->currentApp]->formpop();
		if (method_exists($this->currentForm, 'OnRefresh')) $this->currentForm->OnRefresh();
		//TODO: RECOGER LOS ECHOS COMO COMANDOS.
		$jscommands= new bas_sysx_jsCommands();
		foreach ($actions as $action){
			$this->currentForm->OnAction($action['action'], $action['data']);
			$jscommands->nextjsCommand();
		}
		$this->currentForm->OnPaint('jscommand');
		$jscommands->close();
		echo $jscommands->getjsCommands();
	}
			
}

?>
