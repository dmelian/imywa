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
 * Selección de la instalación que desea ejecutar (code+host+database)
 * @package I0000
 */
class frmselinstall{
	private $actions = array('aceptar', 'cancelar');
	private $installation='';
	
	public function OnLoad(){

		$query = "select useraccess.installation as id, installation.description as value"
		. " from useraccess, installation"
		. " where useraccess.installation = installation.id"
		. " and useraccess.user = substring_index(user(),'@',1)";
	
		$this->tbdef = new bas_dat_tabledef();
		$this->tbdef->addcol('','installation'); $this->tbdef->settemplate('option', array('query'=>$query));
		
			
	}
	
	public function OnPaint(){
		$dlg = new bas_htm_dialog("SELECCIÓN DE APLICATIVO",'dialog',$this->actions,'','Seleccione el aplicativo que desea ejecutar.');
		$card = new bas_htm_card($this->tbdef);
		$dlg->add($card);
		$dlg->printme();
	}
	
	public function OnAction($action, $data){
		switch($action) {
			case 'aceptar':
				//TODO: Guardar la última selección del usuario.
				if (isset($_POST['installation'])) $this->installation = $_POST['installation'];
				return (array('start', $this->installation));
				
			case 'cancelar': return(array('close'));
		}
	}
			
}
?>
