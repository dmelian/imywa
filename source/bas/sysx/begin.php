<?php
class bas_sysx_begin extends bas_frmx_form{
	
	public function OnLoad(){
		parent::OnLoad();
		$this->jsClass= 'bas_sysx_begin';
	}
	
	public function OnStart(){
		$login= new bas_html_inputBox($this, 'login');
		$login->addText('user');
		$login->addPassword('password');
		
		echo $login->jscommand();
		
	}
	
	public function OnOk($data){
		//echo "{\"command\": \"alert\", \"message\": \"Iniciando sesión para el usuario {$data['user']}\"}";
		if ($data['dialog'] == 'login')	return array('createSession', $data['user'], $data['password']);
		else $this->OnStart(); 
	}
	
	public function OnCancel(){
	//TODO mostrar el dialogo: pase buen día.
		$bye= new bas_html_messageBox($this, 'bye', false, 'relogin');
		echo $bye->jscommand();
 
	}
	
	public function OnRelogin(){ $this->OnStart(); }
	
	public function OnAction($action, $data){
		switch($action){
			
		default: 
			echo "{\"command\": \"alert\", \"message\": \"[class: ". get_class($this) .", action: $action] undefined.\"}"; 
		}
	}

}