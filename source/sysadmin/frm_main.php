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
class frm_main{
	private $actions=array ('ok');
	
	function OnLoad(){
	}

	public function OnPaint(){
		$dlg = new bas_htm_dialog("SYSADMIN",'dialog',$this->actions);
		$dlg->printme();
			}

	public function OnAction($action, $data){
		switch($action) {
			
			case 'ok':
					return(array('close'));
				
			default: 
		
		}
	}
	
	
}
?>
