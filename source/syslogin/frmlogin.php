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
 * Formulario de login
 * @package I0000
 */ 
class frmlogin {	
	private $tabledef;
	private $actions=array ('aceptar','cancelar');
	private $user;
	
	function OnLoad(){
		
		$this->tabledef = new bas_dat_tabledef();
		$this->tabledef->addcol(T('data_user'),'user'); 
		$this->tabledef->settemplate('input');
		
		$this->tabledef->addcol(T('data_password'),'password'); 
		$this->tabledef->settemplate('password');
	}

	public function OnPaint(){
		$dlg = new bas_htm_dialog(T('form_login'),'dialog',$this->actions,'',TD('form_login'));
		$tb = new bas_htm_card($this->tabledef,array('user'=>$this->user));
		$dlg->add($tb);
		$dlg->printme();
	}

	public function OnAction($action, $data){
		switch($action) {
			
			case 'aceptar':
					$this->user = $_POST['user'];
					$login = new prglogin($_POST['user'],$_POST['password']);
					if ($login->checkuser()) {
						return (array('switch', 'frmselinstall'));
					} else {
						return (array('open', 'bas_dlg_msgbox', 'error', $login->errormsg));
					}
					break;
					
			case 'cancelar':
					return(array('close'));
				
			default:
		
		}
	}
	
}
?>
