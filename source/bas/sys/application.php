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
 * Manejo de la aplicación
 * @package system
 */
class bas_sys_application{
	public $session;
	public $installationid; #it will be replaced for appid.
	public $nextsequenceno;
	public $user;
	public $password; #TODO: GUARDARLA ENCRIPTADA.
	public $language;
	public $theme;
	public $apps;
	public $dbs; 
	public $dboards;
	
	public function __construct(){
		global $CONFIG;
		
		if (isset($CONFIG['DEFAULTLANGUAGE'])) $this->language= $CONFIG['DEFAULTLANGUAGE'];
		else $this->language= "en";
	}
	
	/**
	 * Guarda los datos de la aplicación/sesión en el servidor. 
	 *
	 */
	function serialize(){
		global $CONFIG;

		$this->session->serialize();
		$sessionfolder = "{$CONFIG['RUNDIR']}{$this->installationid}/{$this->session->sessionno}/";
		$fp = fopen("${sessionfolder}APPDATA","w");
		fwrite($fp, serialize($this));
		fclose($fp);
		chmod("${sessionfolder}APPDATA", $CONFIG['FILERUNDIRMOD']);
	}

	public function newSession($installationid, $user='', $password=''){
		global $ICONFIG;
		
		$this->session = new bas_sys_session();
		$sessionno = $this->session->createsession($installationid);
		
		if ($sessionno != '*') {
			$this->installationid = $installationid;
			$this->nextsequenceno = 1;
			if ($user) {$this->user = $user; $this->password = $password; }
			 
			eval("\$form = new ${ICONFIG['MAINFRM']};");
			$command = method_exists($form, 'OnLoad') ? $form->OnLoad() : array('paint');
			$this->execcommand($command, $form);
			
		} else {
		  # TODO: Informar que no se puede iniciar sesión.
		  echo "No se puede iniciar sesión porque no quedan más números de sesión.";
		}
	
	}
	
	public function sessionfoldererror(){
	}
	
	public function wakeUp(){
		global $CONFIG;
		global $ICONFIG;
		global $_LOG;
		
		$sessionfolder = "{$CONFIG['RUNDIR']}{$this->installationid}/{$this->session->sessionno}/";
		$this->session = unserialize(file_get_contents($sessionfolder."SESDATA"));
		# TODO: Comprobar que la sesion está activa
		
		if ($_POST['sequenceno']!=$this->nextsequenceno){
			# Error de sequencia. Se cierra la sesión.
			$this->session->delsession();
			$dlg = new bas_htm_dialog("ERROR DE SECUENCIA",'error',array('Iniciar otra sesión'));
			$dlg->p('Se ha detectado un error en la secuencia de su sesión.');
			$dlg->p('Esto puede ser causado por utilizar el botón "Atras" de su navegador.');
			$dlg->p('Por motivos de seguridad su sesión ha sido eliminada y tiene que iniciar una nueva sesión.');
			$dlg->printme();
			die();
		} else $this->nextsequenceno++;
		
	
		# TODO: seguir la ejecución de la sesion.
		$form = $this->session->formpop();
		if (method_exists($form, 'OnRefresh')) $command = $form->OnRefresh();
		if (!isset($command)) {
			$command = $form->OnAction(isset($_REQUEST['action']) ? $_REQUEST['action'] : 'undefined',$_REQUEST);
		}
		$this->execcommand($command, $form);
	}
	
	public function xhrwakeUp(){
		global $CONFIG;
		global $_LOG;
		/* WARNING ---------------------------
		 * Como la llamada xmlhttprequest, es masivamente paralela,
		 * si borramos el fichero del top de la pila o lo modificamos,
		 * se producen errores al intentar leer el formulario mientras otro lo escribe.
		 * esto IMPLICA:
		 * QUE NO SE PUEDEN HACER CAMBIOS EN EL FORMULARIO DESDE LLAMADAS XHR,
		 * a no ser que se modifique el modo en que se serialize el formulario y que
		 * varias sesiones puedan acceder leyendo y escribiendo.
		 * 
		 * Lo mismo sucede con el objeto aplicación (NO SE PUEDE SERIALIZAR).
		 */
		
		$sessionfolder = "{$CONFIG['RUNDIR']}{$this->installationid}/{$this->session->sessionno}/";
		$this->session = unserialize(file_get_contents($sessionfolder."SESDATA"));
		# TODO: Comprobar que la sesion está activa
		
		if ($_POST['sequenceno']==$this->nextsequenceno){
			//$form = $this->session->formpop();
			$form = $this->session->topform();
			$form->OnAction(isset($_REQUEST['action']) ? $_REQUEST['action'] : 'saveconfig', $_REQUEST);
			//$this->session->formpush($form);
			//$this->session->updatetopform($form);
			//$this->serialize();
		};		
	}

	private function execcommand($command, $form){
		global $CONFIG;
		global $ICONFIG;
		
		switch ($command[0]){
			case 'open': # Deja el form actual en la pila y abre uno nuevo. 
				$this->session->formpush($form);
				eval("\$form = new ${command[1]};");
				if (method_exists($form, 'OnLoad')) $newcommand = $form->OnLoad();
				if (isset($newcommand)) {
					$this->execcommand($newcommand, $form);
				} else {
					if (isset($command[2])) $form->OnAction($command[2], $command[3]);
					$form->OnPaint();
					$this->session->formpush($form);
					$this->serialize();
				}
				break;		

			case 'startwith': #Vacía toda la pila y abre el formulario enviado.
				if ($this->session->formstackisempty()) $this->session->formpush($form);	
				else $this->session->formemptystack();
				eval("\$form = new ${command[1]};");
				if (method_exists($form, 'OnLoad')) $newcommand = $form->OnLoad();
				if (isset($newcommand)) {
					$this->execcommand($newcommand, $form);
				} else {
					if (isset($command[2])) $form->OnAction($command[2], $command[3]);
					$form->OnPaint();
					$this->session->formpush($form);
					$this->serialize();
				}
				break; 
				
			case 'switch': #Cierra el form actual y abre uno nuevo (No almacena el actual en la pila)
				eval("\$form = new ${command[1]};");
				if (method_exists($form, 'OnLoad')) $newcommand = $form->OnLoad();
				if (isset($newcommand)){
					$this->execcommand($newcommand, $form);
				} else {
					if (isset($command[2])) $form->OnAction($command[2], $command[3]);
					$form->OnPaint();
					$this->session->formpush($form);
					$this->serialize();
				}
				break;
				
			
			case 'close': # Cierra el form actual y si no queda otro termina, sino lo ejecuta.
				$jump= isset($command[1]) ? $command[1] : 1;
				if ($jump > 0) $form = $this->session->formpop($jump);
				if (method_exists($form, 'OnRefresh')) $newcommand = $form->OnRefresh();
				if (isset($newcommand)) {
					$this->execcommand($newcommand, $form);
				} else {
					if (isset($form)) {
						$form->OnPaint();
						$this->session->formpush($form);
						if (method_exists($form, 'OnRefresh')) $newcommand = $form->OnRefresh();
						if (isset($newcommand)) {
							$this->execcommand($newcommand, $form);
						} else {
							$this->serialize();
						}
					
					} else {
						$this->session->delsession();
						$dlg = new bas_htm_dialog("FIN DEL PROGRAMA",'warning',array('Entrar'));
						$dlg->p('Ya puede cerrar la ventana de su navegador.');
						$dlg->p('Pase buen día.');
						$dlg->printme();
					}
				}
				break;
				
			case 'exit': # Cierra el form actual y termina.
				do	$form = $this->session->formpop();
				while (isset($form));
				$this->session->delsession();
				$dlg = new bas_htm_dialog("FIN DEL PROGRAMA",'warning',array('Entrar'));
				$dlg->p('Ya puede cerrar la ventana de su navegador.');
				$dlg->p('Pase buen día.');
				$dlg->printme();
				break;
				
			case 'return': # Como el close pero el formulario que cierra manda datos al anterior a través del OnAction.
				$form = $this->session->formpop();
				if (method_exists($form, 'OnRefresh')) $newcommand = $form->OnRefresh();
				if (isset($newcommand)) {
					$this->execcommand($newcommand, $form);
				} else {//TODO: Ver el error que retorne el último formulario.
					$newcommand2 = $form->OnAction($command[1], $command[2]);
					if ($newcommand2) {
						$this->execcommand($newcommand2, $form);
					} else {
						$form->OnPaint();
						$this->session->formpush($form);
						$this->serialize();
					}
				}
				break;
				
			case 'start': #Inicia la ejecución de una instalación(program+database)
				if ($this->checkinstallation($command[1])){
					$this->session->delsession();
					$installationid = $command[1];
					include "${CONFIG['CFGDIR']}$installationid.cfg.inc";
					$this->newSession($command[1]);
					
				} else {
					$this->session->formpush($form);
					$dlg = new bas_htm_dialog("ERROR DE PROGRAMA",'error',array('Volver'));
					$dlg->p('No se ha podido ejecutar el aplicativo seleccionado.');
					$dlg->p('Por favor pongase en contacto con su administrador.');
					$dlg->printme();
					$this->serialize();
				} 
				break;
				
			case 'pdf':
				if (method_exists($form, 'OnPdf')) {
					$form->OnPdf();
					// si se imprime decrementamos el número de sesion, ya que el navegador abrirá una ventana nueva con el pdf y se queda en la ventana anterior
					$this->nextsequenceno--; 
				} else {
					$dlg = new bas_htm_dialog("ERROR DE IMPRESION",'error',array('Volver'));
					$dlg->p('No se ha podido encontrar la función de impresión a pdf para este formulario.');
					$dlg->p('Por favor pongase en contacto con su administrador.');
					$dlg->printme();
				} 
				$this->session->formpush($form);
				$this->serialize(); 
				break;
				
			case 'csv':
				if (method_exists($form, 'OnCsv')) {
					$form->OnCsv();
					// si se imprime decrementamos el número de sesion, ya que el navegador abrirá una ventana nueva con el pdf y se queda en la ventana anterior
					$this->nextsequenceno--;
				} else {
					$dlg = new bas_htm_dialog("ERROR DE PROGRAMA",'error',array('Volver'));
					$dlg->p('No se ha podido encontrar la función de conversion a csv para este formulario.');
					$dlg->p('Por favor pongase en contacto con su administrador.');
					$dlg->printme();
				}
				$this->session->formpush($form);
				$this->serialize();
				break;
				
			case 'stay':
				// Utilizada para crear un download y mantenerse en la página anterior. (como en el pdf pero sin generación)
				$this->nextsequenceno--;
				$this->session->formpush($form);
				$this->serialize(); 
				break;
				

			default: #comportamiento por defecto.
				$form->OnPaint();
				$this->session->formpush($form);
				$this->serialize();
		}
	}
	
	
	/**
	 * Comprueba la instalación pasada está configurada
	 *
	 * @param integer $installation
	 * @return bool
	 */
	public function checkinstallation($installation){
		return true;
	}

	
}
	
?>
