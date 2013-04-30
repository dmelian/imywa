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
 * Manejo de sesiones
 * @package system
 */
class bas_sys_session {
	public $sessionno, $sessiondir;
	private $stacktop=0;
	private $breadCrumb=array();

		/**
	 * Guarda los datos de la aplicación/sesión en el servidor. 
	 *
	 */
	function serialize(){
		global $CONFIG;

		if (isset($this->sessionno) && ($this->sessionno!='*')){
			$fp = fopen("$this->sessiondir/SESDATA","w");
			fwrite($fp, serialize($this));
			fclose($fp);
			chmod("$this->sessiondir/SESDATA", $CONFIG['FILERUNDIRMOD']);
		}
	}
	
	
	/**
	 * Crea una nueva sesión.
	 * Crea un identificador nuevo de sesión y los directorios de backup en el servidor.
	 * Toma un número aleatorio de 24 bits y lo transformamos en hexadecimal (6 digitos).
	 * Crea el directorio donde almacenar los datos de la sesion. Si el directorio existe probamos con otro.
	 * Finaliza llamando al método onWakeup
	 * @param string $sessionbasedir
	 * @return string
	 */
	public function createsession($sessionbasedir) {
		global $CONFIG;
		
		$intentos = 0;
		do {
			$sessionbasedir = "${CONFIG['RUNDIR']}$sessionbasedir";
			if (!file_exists($sessionbasedir)){
				mkdir($sessionbasedir);
				chmod($sessionbasedir, $CONFIG['RUNDIRMOD']);
			}
			$this->sessionno = 'X' . str_pad(dechex(mt_rand(0,16777215)), 6, '0', STR_PAD_LEFT);
			$this->sessiondir = "$sessionbasedir/$this->sessionno";
			if (file_exists($this->sessiondir)) {
				# TODO: Comprobar que se trata de una sesión activa, sino borrarla y tomarla como nueva.
				$this->sessionno = '*';
			} else {
				mkdir($this->sessiondir);
				chmod($this->sessiondir, $CONFIG['RUNDIRMOD']);
				mkdir("$this->sessiondir/forms");
				chmod("$this->sessiondir/forms", $CONFIG['RUNDIRMOD']);
			}
		} while (($this->sessionno == '*') && ($intentos++ < 5));

		return $this->sessionno;
	}
	
	/**
	 * Elimina los datos de sesion.
	 * Borra todos los ficheros y directorios de la sesión guardados en el servidor.
	 */
	public function delsession() {
		global $CONFIG;

		$this->sessionno='';
		while ($this->stacktop > 0) {
			$fname = str_pad($this->stacktop--, 4, '0', STR_PAD_LEFT);
			unlink ("$this->sessiondir/forms/F$fname");
		}
		rmdir ("$this->sessiondir/forms");
		unlink ("$this->sessiondir/SESDATA");
		unlink ("$this->sessiondir/APPDATA");
		rmdir ("$this->sessiondir");

	}

	# FORMULARIOS
	# ------------------------------------------------------------------

	/**
	 * Carga el último formulario guardado en la pila.
	 * Inicializa un formulario nuevo con los datos almacenados en el top la pila de formularios de servidor.
	 * Acto seguido elimina el fichero del top de la pila (del disco).
	 * @return form_object
	 */
	public function formpop($jump=1){
		global $CONFIG;
		
		$form= false;
		while ($jump > 0 && $this->stacktop > 0) {
			$fname = str_pad($this->stacktop--, 4, '0', STR_PAD_LEFT);
			if (--$jump == 0) {
				$form= unserialize(file_get_contents("$this->sessiondir/forms/F$fname"));
			}
			unlink ("$this->sessiondir/forms/F$fname");
			array_pop($this->breadCrumb);
		}
		if ($form) return $form;
	}

	/**
	 * 
	 * Retorna el top de la pila pero no realiza ningún pop.
	 */
	public function topform(){
		global $CONFIG;

		if ($this->stacktop > 0) {
			$fname = str_pad($this->stacktop, 4, '0', STR_PAD_LEFT);
			$form = unserialize(file_get_contents("$this->sessiondir/forms/F$fname"));
			return $form;
		}
	}
	
	
	/**
	 * 
	 * Vacía toda la pila menos el último (menu principal).
	 */
	public function formemptystack(){
		global $CONFIG;
		
		while ($this->stacktop > 1) {
			$fname = str_pad($this->stacktop--, 4, '0', STR_PAD_LEFT);
			unlink ("$this->sessiondir/forms/F$fname");
		}
	}
	
	public function formstackisempty(){
		return $this->stacktop == 0;
	}
	
	/**
	 * Salva el formulario en el top de la pila
	 * @param form_object $form
	 */
	public function formpush(&$form){
		global $CONFIG;

		$caption= method_exists($form, "getBreadCrumbCaption")? $form->getBreadCrumbCaption() : '?';
		array_push($this->breadCrumb, $caption);
		$fname = "$this->sessiondir/forms/F" . str_pad(++$this->stacktop, 4, '0', STR_PAD_LEFT);
		$fp = fopen($fname,'w');
		fwrite($fp, serialize($form));
		fclose($fp);
		chmod($fname, 0666);

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
