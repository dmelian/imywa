<?php
class frmx_dialog_dialog extends bas_frmx_dialog{

	function OnLoad(){
		$this->title= "Dialogo de prueba";
		$this->actions= array('aceptar','cancelar');
	}
	
	public function OnPaintContent(){
		echo '<table>';
		echo '<tr><td>Usuario</td><td><input id="user"></td>';
		echo '<tr><td>Contraseña</td><td><input id="password"></td>';
		echo '</table>';
	}
	
	public function OnAction($action, $data){
		switch($action) {
				
			case 'aceptar':
				break;
					
			case 'cancelar':
				return(array('close'));
	
			default:
	
		}
	}
	
	
	
}
?>