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
class bas_frmx_testform{
	private $frames= array();

	//Theme ??

	function addFrame($frame, $id){
		$this->frames[$id]= $frame;
	}
	
	function OnPrepareData(){
		
	}
	
	function OnCommand($command, $data){
	
	}
	
	// Form 
	
	function OnLoad(){
		$this->OnPrepareData();
		foreach($this->frames as $frame) $frame->OnPrepareData();		
	}
	
	function OnPaint(){
		// Para cada frame, crear una división con su clase y su id.
		$theme= 'default';
		
		echo "<html><head>";
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html\" charset=\"utf-8\">";
		echo "<script type=\"text/javascript\" src=\"script/frmxform.js\"></script>";
		echo "<script type=\"text/javascript\" src=\"script/frmxlist.js\"></script>";
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"theme/default/style/frmxlist.css\">";
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"theme/default/style/frmxform.css\">";
		echo "</head>";
//		echo "<body onload=\"javascript:onload('default');\">";
		echo "<body onload=\"new frmxform('default');\">";
		echo "<form method=\"post\">";
		echo getsessionstamp();
		foreach($this->frames as $id => $frame) echo "<div class=\"frmxlist\" id=\"$id\"></div>"; //Cambiar la clase también.
		echo "</form></body></html>";
		
	}
	
	function OnAction($action, $data){
		if ($action == 'xhrcommand'){
			
			if (isset($data['frameid'])) {
				if (isset($this->frames[$data['frameid']])){
					$this->frames[$data['frameid']]->OnCommand($data['command'], $data);
				} else {/* LOG. No se encuentra el frame frameid*/}
				
			} else $this->OnCommand($data['command'], $data);
			
		} else switch($action){
			//
		}
	}
		
}
?>
